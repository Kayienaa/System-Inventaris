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
        $response->assertSee('Detail');
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
}
