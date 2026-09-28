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
        $response->assertViewHas('popular_assets');
        $response->assertViewHas('active_borrowers');
        $response->assertViewHas('total_aset');
        $response->assertViewHas('barang_tersedia');
        $response->assertViewHas('barang_dipinjam');
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

        // Banner Aksi Cepat (Quick Action CTA) peminjam wajib terlihat
        $response->assertSee('Butuh Perangkat untuk Praktik TEFA?');
        $response->assertSee('Ajukan peminjaman laptop atau smartphone inventaris TEFA SMKN 1 Bangsri dengan mudah dan transparan.');
        $response->assertSee('Mulai Pinjam Barang');
        $response->assertSee(route('assets.index'));
        $response->assertSee('Mas Donny');
    }

    public function test_admin_does_not_see_borrower_quick_action_cta_banner(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Butuh Perangkat untuk Praktik TEFA?');
        $response->assertDontSee('Mulai Pinjam Barang');
    }

    public function test_weekly_borrowing_chart_excludes_rejected_and_cancelled_transactions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Siswa Test Trend']);
        $siswa->assignRole('siswa');

        $assets = Asset::take(3)->get();
        $asset1 = $assets[0];
        $asset2 = $assets[1];
        $asset3 = $assets[2];

        // 1. Transaksi aktif riil (Borrowed) - Wajib terhitung
        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset1->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        // 2. Transaksi ditolak (Rejected) - TIDAK boleh terhitung
        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset2->id,
            'status' => BorrowingStatus::Rejected,
            'requested_at' => now(),
            'due_at' => now()->addDays(3),
            'rejection_reason' => 'Unit sedang maintenance',
        ]);

        // 3. Transaksi dibatalkan (Cancelled) - TIDAK boleh terhitung
        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset3->id,
            'status' => BorrowingStatus::Cancelled,
            'requested_at' => now(),
            'due_at' => now()->addDays(3),
            'cancelled_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $chartData = $response->viewData('chart_data');

        // Total akumulasi chart data harus bernilai 1 (hanya transaksi Borrowed), bukan 3
        $this->assertEquals(1, array_sum($chartData));
    }
}
