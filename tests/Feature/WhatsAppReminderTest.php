<?php

namespace Tests\Feature;

use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Borrowing;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_phone_number_normalization(): void
    {
        // Diawali 620 (hapus 0 yang terjebak setelah 62)
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('62081234567890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('+62 0812 3456 7890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('+62-0812-3456-7890'));

        // Diawali 62
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('6281234567890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('+62 812 3456 7890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('+62-812-3456-7890'));

        // Diawali 08
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('081234567890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('0812-3456-7890'));

        // Diawali 0 (non 08)
        $this->assertEquals('62211234567', WhatsAppNotificationService::normalizePhoneNumber('0211234567'));

        // Diawali 8 langsung
        $this->assertEquals('6281234567890', WhatsAppNotificationService::normalizePhoneNumber('81234567890'));

        // Kosong atau null
        $this->assertEquals('', WhatsAppNotificationService::normalizePhoneNumber(''));
        $this->assertEquals('', WhatsAppNotificationService::normalizePhoneNumber(null));
    }

    public function test_phone_number_display_formatting(): void
    {
        $this->assertEquals('+62 812-3456-7890', WhatsAppNotificationService::formatDisplayPhoneNumber('081234567890'));
        $this->assertEquals('+62 812-3456-7890', WhatsAppNotificationService::formatDisplayPhoneNumber('62081234567890'));
        $this->assertEquals('+62 812-3456-7890', WhatsAppNotificationService::formatDisplayPhoneNumber('+62 812 3456 7890'));
        $this->assertEquals('-', WhatsAppNotificationService::formatDisplayPhoneNumber(''));
        $this->assertEquals('-', WhatsAppNotificationService::formatDisplayPhoneNumber(null));
    }

    public function test_build_reminder_message_for_approaching_deadline(): void
    {
        $siswa = User::factory()->create(['name' => 'Budi Santoso']);
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '7890',
            'class_name' => 'XII RPL 2',
            'phone' => '081234567890',
        ]);

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
            'borrower_note' => 'Keperluan tugas akhir',
        ]);

        $message = WhatsAppNotificationService::buildReminderMessage($borrowing);

        $this->assertStringContainsString('Budi Santoso', $message);
        $this->assertStringContainsString('NIS: 7890', $message);
        $this->assertStringContainsString('Kelas: XII RPL 2', $message);
        $this->assertStringContainsString($asset->name, $message);
        $this->assertStringContainsString($asset->asset_code, $message);
        $this->assertStringContainsString('MENDEKATI TENGGAT', $message);
        $this->assertStringContainsString('TEFA SMKN 1 Bangsri', $message);

        $url = WhatsAppNotificationService::getWhatsAppUrl($borrowing);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
    }

    public function test_build_reminder_message_for_overdue_borrowing(): void
    {
        $guru = User::factory()->create(['name' => 'Pak Joko Widodo']);
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198001012010011001',
            'phone' => '085712345678',
        ]);

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $guru->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDays(5),
            'borrowed_at' => now()->subDays(5),
            'due_at' => now()->subDays(2),
        ]);

        $message = WhatsAppNotificationService::buildReminderMessage($borrowing);

        $this->assertStringContainsString('Pak Joko Widodo', $message);
        $this->assertStringContainsString('NIP: 198001012010011001', $message);
        $this->assertStringContainsString('SUDAH MELEWATI BATAS (OVERDUE)', $message);
        $this->assertStringContainsString('TEFA SMKN 1 Bangsri', $message);

        $url = WhatsAppNotificationService::getWhatsAppUrl($borrowing);
        $this->assertStringStartsWith('https://wa.me/6285712345678?text=', $url);
    }

    public function test_monitoring_index_displays_whatsapp_button_for_borrowed_and_overdue_only(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Siti Aminah']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '11223',
            'class_name' => 'X RPL 1',
            'phone' => '08987654321',
        ]);

        $assets = Asset::take(2)->get();

        // 1. Borrowed transaction (should display WhatsApp button)
        $borrowed = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $assets[0]->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        // 2. Returned transaction (should NOT display WhatsApp button)
        $returned = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $assets[1]->id,
            'status' => BorrowingStatus::Returned,
            'requested_at' => now()->subDays(4),
            'borrowed_at' => now()->subDays(4),
            'due_at' => now()->subDays(1),
            'returned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.index'));

        $response->assertStatus(200);
        $response->assertSee('title="Kirim Pengingat WhatsApp ke Peminjam"', false);
        $response->assertSee('https://wa.me/628987654321', false);
    }

    public function test_borrowing_detail_json_includes_wa_url(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Eko Prasetyo']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '99887',
            'phone' => '082133445566',
        ]);

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.borrowings.show', $borrowing));

        $response->assertStatus(200);
        $response->assertJsonStructure(['wa_url']);
        $this->assertStringStartsWith('https://wa.me/6282133445566?text=', $response->json('wa_url'));
    }

    public function test_whatsapp_gateway_config_is_loaded(): void
    {
        $config = config('services.whatsapp');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('admin_number', $config);
        $this->assertArrayHasKey('api_token', $config);
        $this->assertArrayHasKey('api_url', $config);
        $this->assertEquals('https://api.fonnte.com/send', $config['api_url']);
    }

    public function test_whatsapp_url_returns_null_when_borrower_phone_is_empty(): void
    {
        $userWithoutPhone = User::factory()->create(['name' => 'User Tanpa HP']);
        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $userWithoutPhone->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $url = WhatsAppNotificationService::getWhatsAppUrl($borrowing);

        $this->assertNull($url);
    }

    public function test_whatsapp_url_reads_phone_from_alternative_profile_attributes(): void
    {
        $siswa = User::factory()->create(['name' => 'Budi Alternatif']);
        $profile = SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '8821',
            'phone' => '087711223344',
        ]);

        $asset = Asset::first();
        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $url = WhatsAppNotificationService::getWhatsAppUrl($borrowing);
        $this->assertStringStartsWith('https://wa.me/6287711223344?text=', $url);
    }

    public function test_whatsapp_url_logs_warning_when_borrower_phone_is_empty(): void
    {
        \Illuminate\Support\Facades\Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'tidak memiliki nomor WhatsApp terdaftar');
            });

        $userWithoutPhone = User::factory()->create(['name' => 'User Log Test']);
        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $userWithoutPhone->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $this->assertNull(WhatsAppNotificationService::getWhatsAppUrl($borrowing));
    }

    public function test_fix_borrower_phones_command_does_not_create_dummy_phones_and_normalizes_existing(): void
    {
        // 1. Siswa tanpa nomor HP (tidak boleh diisi dummy fiktif)
        $userWithoutPhone = User::factory()->create(['name' => 'Siswa Belum Ada HP']);
        SiswaProfile::create([
            'user_id' => $userWithoutPhone->id,
            'nis' => '9999',
            'phone' => null,
        ]);

        // 2. Siswa dengan nomor HP yang perlu normalisasi
        $userWithPhone = User::factory()->create(['name' => 'Siswa Ada HP']);
        SiswaProfile::create([
            'user_id' => $userWithPhone->id,
            'nis' => '8888',
            'phone' => '0812-3456-7890',
        ]);

        $assets = Asset::take(2)->get();
        Borrowing::create([
            'borrower_user_id' => $userWithoutPhone->id,
            'asset_id' => $assets[0]->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);
        Borrowing::create([
            'borrower_user_id' => $userWithPhone->id,
            'asset_id' => $assets[1]->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $this->artisan('tefa:fix-borrower-phones')
            ->assertSuccessful();

        $userWithoutPhone->siswaProfile->refresh();
        $this->assertNull($userWithoutPhone->siswaProfile->phone, 'Siswa tanpa HP tidak boleh diisi nomor dummy fiktif.');

        $userWithPhone->siswaProfile->refresh();
        $this->assertEquals('6281234567890', $userWithPhone->siswaProfile->phone);
    }

    public function test_monitoring_index_shows_disabled_whatsapp_button_when_phone_is_missing(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswaWithoutPhone = User::factory()->create(['name' => 'Doni Tanpa HP']);
        $siswaWithoutPhone->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswaWithoutPhone->id,
            'nis' => '77665',
            'class_name' => 'X RPL 2',
            'phone' => null,
        ]);

        $asset = Asset::first();

        Borrowing::create([
            'borrower_user_id' => $siswaWithoutPhone->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.index'));

        $response->assertStatus(200);
        $response->assertSee('title="Nomor WhatsApp peminjam belum terdaftar di profil"', false);
        $response->assertSee('disabled', false);
    }
}
