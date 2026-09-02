<?php

namespace Tests\Feature;

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

class BorrowingExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_categories_and_assets_seeder_contain_only_tefa_laptop_and_hp(): void
    {
        // Pastikan kategori hanya 2: Laptop dan HP
        $categories = AssetCategory::all();
        $this->assertCount(2, $categories);
        $this->assertTrue($categories->pluck('code')->contains('CAT-LPT'));
        $this->assertTrue($categories->pluck('code')->contains('CAT-HP'));

        // Pastikan jumlah master aset tepat 30 (27 Laptop & 3 HP)
        $assets = Asset::all();
        $this->assertCount(30, $assets);

        $laptops = Asset::whereHas('category', fn ($q) => $q->where('code', 'CAT-LPT'))->get();
        $hps = Asset::whereHas('category', fn ($q) => $q->where('code', 'CAT-HP'))->get();

        $this->assertCount(27, $laptops);
        $this->assertCount(3, $hps);
    }

    public function test_admin_can_export_borrowings_csv(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Budi Siswa']);
        $siswa->assignRole('siswa');

        $asset = Asset::first();

        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Catatan tes ekspor',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.export-excel'));

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));

        // Ambil isi stream output
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('Nama Peminjam', $csvContent);
        $this->assertStringContainsString('Identitas (NIS/NIP)', $csvContent);
        $this->assertStringContainsString('Kode Barang', $csvContent);
        $this->assertStringContainsString('Budi Siswa', $csvContent);
        $this->assertStringContainsString($asset->asset_code, $csvContent);
    }

    public function test_admin_can_view_borrowings_pdf_report(): void
    {
        $admin = User::factory()->create(['name' => 'Admin Pengelola']);
        $admin->assignRole('admin');

        $siswa = User::factory()->create(['name' => 'Citra Siswi']);
        $siswa->assignRole('siswa');

        $asset = Asset::first();

        Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.borrowings.export-pdf'));

        $response->assertStatus(200);
        $response->assertSee('SMK Negeri 1 Bangsri');
        $response->assertSee('Laporan Rekapitulasi Riwayat Peminjaman Aset');
        $response->assertSee('Citra Siswi');
        $response->assertSee($asset->name);
        $response->assertSee('Kepala Lab / Pembimbing TEFA');
        $response->assertSee('Pengelola Inventaris TE-VAULT');
    }

    public function test_non_admin_cannot_export_borrowings(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $this->actingAs($siswa)->get(route('admin.borrowings.export-excel'))->assertStatus(403);
        $this->actingAs($siswa)->get(route('admin.borrowings.export-pdf'))->assertStatus(403);
    }
}
