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
        $response->assertSee('Integrasi SiPintu Gateway &amp; SIJUNA', false);

        // Verify view data
        $response->assertViewHas('chart_labels');
        $response->assertViewHas('chart_data');
        $response->assertViewHas('weekly_period_label');
        $response->assertViewHas('weekly_history');
        $response->assertViewHas('popular_assets');
        $response->assertViewHas('active_borrowers');
        $response->assertViewHas('total_aset');
        $response->assertViewHas('barang_tersedia');
        $response->assertViewHas('barang_dipinjam');

        $response->assertSee('Lihat History Mingguan');
        $response->assertSee($response->viewData('weekly_period_label'));

        // Chart labels harus 7 hari (Senin s.d. Minggu)
        $labels = $response->viewData('chart_labels');
        $this->assertCount(7, $labels);
        $this->assertStringStartsWith('Senin', $labels[0]);
        $this->assertStringStartsWith('Minggu', $labels[6]);
    }

    public function test_admin_can_access_weekly_history_endpoint(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson(route('admin.dashboard.weekly-history'));

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'week_number',
                    'is_current',
                    'period',
                    'start_date',
                    'end_date',
                    'total_transactions',
                    'top_assets',
                    'top_borrowers',
                ],
            ],
        ]);
        $this->assertCount(8, $response->json('data'));
    }

    public function test_super_admin_can_access_weekly_history_endpoint(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->getJson(route('admin.dashboard.weekly-history'));

        $response->assertOk();
    }

    public function test_non_admin_cannot_access_weekly_history_endpoint(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        \App\Models\SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '11223',
            'phone' => '081234567890',
        ]);

        $response = $this->actingAs($siswa)->get(route('admin.dashboard.weekly-history'));

        $response->assertForbidden();
    }

    public function test_non_admin_does_not_see_sipintu_gateway_widget_on_dashboard(): void
    {
        $siswa = User::factory()->create(['name' => 'Siswa Test']);
        $siswa->assignRole('siswa');
        \App\Models\SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($siswa)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Inventaris');
        $response->assertDontSee('Integrasi SiPintu Gateway &amp; SIJUNA', false);
        $response->assertDontSee('DATA PENGGUNA (SISWA)');
        $response->assertDontSee('DATA GURU');
        $response->assertDontSee('GATEWAY STATUS');
    }
}
