<?php

namespace Tests\Feature;

use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Borrowing;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowerWhatsAppConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_build_borrower_confirmation_message_for_siswa(): void
    {
        $siswa = User::factory()->create(['name' => 'Budi Santoso']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '081234567890',
            'class_name' => 'XII RPL 1',
        ]);

        $category = AssetCategory::create([
            'code' => 'ELK-01',
            'name' => 'Elektronik',
        ]);
        $asset = Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Laptop Asus ROG Zephyrus',
            'asset_code' => 'TI-LTP-001',
            'status' => 'tersedia',
        ]);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->setTime(15, 15, 0),
            'purpose_category' => 'praktik',
            'urgency_level' => 'biasa',
        ]);

        $message = WhatsAppNotificationService::buildBorrowerConfirmationMessage($borrowing);

        $this->assertStringContainsString('Halo Admin TEFA, saya telah mengajukan peminjaman unit di SITEFA:', $message);
        $this->assertStringContainsString('- Nama Peminjam: Budi Santoso (NIS: 12345)', $message);
        $this->assertStringContainsString('- Unit Barang: Laptop Asus ROG Zephyrus', $message);
        $this->assertStringContainsString('- Kode Transaksi: #TRX-' . str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT), $message);
        $this->assertStringContainsString('- Waktu Pengajuan: ' . $borrowing->requested_at->format('d/m/Y H:i') . ' WIB', $message);
        $this->assertStringContainsString('Mohon untuk mengecek dan menyetujui pengajuan peminjaman saya. Terima kasih.', $message);
    }

    public function test_build_borrower_confirmation_message_for_guru(): void
    {
        $guru = User::factory()->create(['name' => 'Pak Joko Widodo']);
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198001012010011001',
            'phone' => '085712345678',
        ]);

        $category = AssetCategory::create([
            'code' => 'CAM-01',
            'name' => 'Kamera',
        ]);
        $asset = Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Sony Alpha A7 IV',
            'asset_code' => 'TI-CAM-002',
            'status' => 'tersedia',
        ]);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $guru->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->addDays(3),
            'purpose_category' => 'mengajar',
            'urgency_level' => 'mendesak',
        ]);

        $message = WhatsAppNotificationService::buildBorrowerConfirmationMessage($borrowing);

        $this->assertStringContainsString('- Nama Peminjam: Pak Joko Widodo (NIP: 198001012010011001)', $message);
        $this->assertStringContainsString('- Unit Barang: Sony Alpha A7 IV', $message);
        $this->assertStringContainsString('- Kode Transaksi: #TRX-' . str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT), $message);
    }

    public function test_borrower_confirmation_url_uses_dynamic_system_setting_number(): void
    {
        SystemSetting::set('tefa_admin_whatsapp', '082223005860');

        $siswa = User::factory()->create(['name' => 'Budi Santoso']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '081234567890',
        ]);

        $category = AssetCategory::create([
            'code' => 'ELK-02',
            'name' => 'Elektronik',
        ]);
        $asset = Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Laptop Asus ROG',
            'asset_code' => 'TI-LTP-001',
            'status' => 'tersedia',
        ]);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->setTime(15, 15, 0),
        ]);

        $url = WhatsAppNotificationService::getBorrowerConfirmationUrl($borrowing);

        // Nomor harus dinormalisasi ke format internasional 628...
        $this->assertStringStartsWith('https://wa.me/6282223005860?text=', $url);

        $message = WhatsAppNotificationService::buildBorrowerConfirmationMessage($borrowing);
        $this->assertStringContainsString(rawurlencode($message), $url);
    }

    public function test_borrower_confirmation_url_falls_back_to_config_number(): void
    {
        SystemSetting::where('key', 'tefa_admin_whatsapp')->delete();
        config(['services.whatsapp.admin_number' => '081298765432']);

        $siswa = User::factory()->create(['name' => 'Siswa Test']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '081234567890',
        ]);

        $category = AssetCategory::create([
            'code' => 'ELK-03',
            'name' => 'Elektronik',
        ]);
        $asset = Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Laptop Test',
            'asset_code' => 'TI-LTP-002',
            'status' => 'tersedia',
        ]);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->setTime(15, 15, 0),
        ]);

        $url = WhatsAppNotificationService::getBorrowerConfirmationUrl($borrowing);

        $this->assertStringStartsWith('https://wa.me/6281298765432?text=', $url);
    }

    public function test_submitting_borrowing_request_redirects_with_wa_confirmation_url(): void
    {
        SystemSetting::set('tefa_admin_whatsapp', '6282223005860');

        $siswa = User::factory()->create(['name' => 'Ahmad Siswa']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '99887',
            'phone' => '081234567890',
        ]);

        $category = AssetCategory::create([
            'code' => 'ELK-04',
            'name' => 'Elektronik',
        ]);
        $asset = Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Kamera Canon EOS',
            'asset_code' => 'TI-CAM-099',
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($siswa)->post(route('assets.borrow.store', $asset), [
            'purpose_category' => 'praktik',
            'urgency_level' => 'biasa',
            'borrower_note' => 'Untuk dokumentasi tugas sekolah',
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('wa_confirmation_url');

        $waUrl = session('wa_confirmation_url');
        $this->assertStringStartsWith('https://wa.me/6282223005860?text=', $waUrl);
    }

    public function test_mine_page_displays_vintage_brown_wa_confirmation_banner(): void
    {
        $siswa = User::factory()->create(['name' => 'Ahmad Siswa']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '99887',
            'phone' => '081234567890',
        ]);

        $waUrl = 'https://wa.me/6282223005860?text=Halo%20Admin%20TEFA';

        $response = $this->actingAs($siswa)
            ->withSession([
                'success' => 'Permohonan peminjaman berhasil diajukan! Menunggu persetujuan Admin.',
                'wa_confirmation_url' => $waUrl,
            ])
            ->get(route('borrowings.mine'));

        $response->assertOk();
        $response->assertSee('silahkan konfirmasi ulang melalui nomor wa admin TEFA');
        $response->assertSee('Konfirmasi via WhatsApp Admin');
        $response->assertSee('href="' . $waUrl . '"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
    }

    public function test_mine_page_pending_borrowing_card_has_wa_confirmation_link(): void
    {
        SystemSetting::set('tefa_admin_whatsapp', '6282223005860');

        $siswa = User::factory()->create(['name' => 'Ahmad Siswa']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '99887',
            'phone' => '081234567890',
        ]);

        $category = AssetCategory::create([
            'code' => 'ELK-05',
            'name' => 'Elektronik',
        ]);
        $asset = Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Kamera Canon EOS',
            'asset_code' => 'TI-CAM-099',
            'status' => 'tersedia',
        ]);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->setTime(15, 15, 0),
            'purpose_category' => 'praktik',
            'urgency_level' => 'biasa',
        ]);

        $response = $this->actingAs($siswa)->get(route('borrowings.mine'));

        $response->assertOk();
        $response->assertSee('silahkan konfirmasi ulang melalui nomor wa admin TEFA');
        $response->assertSee('Hubungi Admin via WA');
        $waUrl = WhatsAppNotificationService::getBorrowerConfirmationUrl($borrowing);
        $response->assertSee('href="' . $waUrl . '"', false);
        $this->assertEquals(1, substr_count($response->getContent(), 'Hubungi Admin via WA'));
    }
}
