<?php

namespace Tests\Feature;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Borrowing;
use App\Models\User;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_user_can_view_dashboard_with_analytics_and_leaderboards(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Siswa Aktif']);
        $siswa->assignRole('siswa');

        $asset = Asset::first();

        // Buat peminjaman
        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
        ]);
        $asset->update(['availability_status' => AssetAvailabilityStatus::Dipinjam]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Inventaris');
        $response->assertSee('Visualisasi Tren Peminjaman');
        $response->assertSee('Top 5 Aset Paling Sering Dipinjam');
        $response->assertSee('Top 5 Peminjam Teraktif');
        $response->assertSee($asset->name);
        $response->assertSee('Siswa Aktif');

        // Verify view data
        $response->assertViewHas('chart_labels');
        $response->assertViewHas('chart_data');
        $response->assertViewHas('popular_assets');
        $response->assertViewHas('active_borrowers');
        $response->assertViewHas('total_aset');
        $response->assertViewHas('barang_tersedia');
        $response->assertViewHas('barang_dipinjam');
    }
}
