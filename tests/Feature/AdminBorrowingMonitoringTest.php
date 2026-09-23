<?php

namespace Tests\Feature;

use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Borrowing;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBorrowingMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_admin_can_view_borrowing_monitoring_center(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Aditya Pratama']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'class_name' => 'XII RPL 1',
        ]);

        $asset = Asset::first();

        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Praktikum pemrograman web',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Monitoring Peminjaman');
        $response->assertSee('Aditya Pratama');
        $response->assertSee('NIS: 12345');
        $response->assertSee($asset->asset_code);
        $response->assertSee($asset->name);
        $response->assertSee('Dipinjam');
        $response->assertSee('Kelola');
    }

    public function test_admin_can_view_borrowing_detail_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Bambang Sudiro']);
        $siswa->assignRole('siswa');

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Catatan keperluan uji coba show',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.show', $borrowing));

        $response->assertStatus(200);
        $response->assertSee('Transaksi #TRX-' . str_pad((string) $borrowing->id, 5, '0', STR_PAD_LEFT));
        $response->assertSee('Bambang Sudiro');
        $response->assertSee($asset->name);
        $response->assertSee('Catatan keperluan uji coba show');
    }

    public function test_admin_can_fetch_borrowing_detail_as_json(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Carla Siswi']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '54321',
            'class_name' => 'XI RPL 2',
        ]);

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Keperluan JSON Test',
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.borrowings.show', $borrowing));

        $response->assertStatus(200);
        $response->assertJsonPath('borrower.name', 'Carla Siswi');
        $response->assertJsonPath('borrower.identity', 'NIS: 54321');
        $response->assertJsonPath('asset.asset_code', $asset->asset_code);
        $response->assertJsonPath('borrower_note', 'Keperluan JSON Test');
    }

    public function test_admin_can_filter_borrowings_by_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Dedi Peminjam']);
        $siswa->assignRole('siswa');

        $asset1 = Asset::all()[0];
        $asset2 = Asset::all()[1];

        // 1 Active Borrowed
        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset1->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        // 1 Returned
        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset2->id,
            'status' => BorrowingStatus::Returned,
            'requested_at' => now()->subDays(5),
            'borrowed_at' => now()->subDays(5),
            'due_at' => now()->subDays(2),
            'returned_at' => now()->subDay(),
        ]);

        // Filter status borrowed
        $response = $this->actingAs($admin)->get(route('admin.borrowings.index', ['status' => 'borrowed']));
        $response->assertStatus(200);
        $response->assertSee($asset1->asset_code);
        $response->assertDontSee($asset2->asset_code);
    }

    public function test_non_admin_cannot_access_admin_borrowings(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);

        $this->actingAs($siswa)->get(route('admin.borrowings.index'))->assertStatus(403);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => Asset::first()->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        $this->actingAs($siswa)->get(route('admin.borrowings.show', $borrowing))->assertStatus(403);
    }

    public function test_admin_can_reject_pending_borrowing_with_reason(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Fajar Siswa']);
        $siswa->assignRole('siswa');

        $asset = Asset::first();
        $asset->update(['availability_status' => \App\Enums\AssetAvailabilityStatus::Dipesan]);

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Permohonan pinjam untuk praktikum',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.borrowings.index'))
            ->post(route('admin.borrowings.reject', $borrowing), [
                'rejection_reason' => 'Barang sedang dalam jadwal perawatan lab.',
            ]);

        $response->assertRedirect(route('admin.borrowings.index'));
        $response->assertSessionHas('success');

        $borrowing->refresh();
        $this->assertEquals(BorrowingStatus::Rejected, $borrowing->status);
        $this->assertEquals('Barang sedang dalam jadwal perawatan lab.', $borrowing->rejection_reason);
        $this->assertEquals($admin->id, $borrowing->rejected_by_user_id);
        $this->assertNotNull($borrowing->rejected_at);

        $asset->refresh();
        $this->assertEquals(\App\Enums\AssetAvailabilityStatus::Tersedia, $asset->availability_status);

        // Verify JSON detail representation reflects rejection
        $jsonResponse = $this->actingAs($admin)->getJson(route('admin.borrowings.show', $borrowing));
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonPath('rejection_reason', 'Barang sedang dalam jadwal perawatan lab.');
        $jsonResponse->assertJsonPath('dates.due_at', 'Ditolak');
    }

    public function test_admin_cannot_reject_without_rejection_reason(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => Asset::first()->id,
            'status' => BorrowingStatus::Pending,
            'requested_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.borrowings.index'))
            ->post(route('admin.borrowings.reject', $borrowing), [
                'rejection_reason' => '',
            ]);

        $response->assertSessionHasErrors('rejection_reason');

        $borrowing->refresh();
        $this->assertEquals(BorrowingStatus::Pending, $borrowing->status);
    }
}
