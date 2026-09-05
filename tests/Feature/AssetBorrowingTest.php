<?php

namespace Tests\Feature;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Borrowing;
use App\Models\User;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetBorrowingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    public function test_user_can_view_katalog_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $response = $this->actingAs($user)->get(route('assets.index'));

        $response->assertStatus(200);
        $response->assertSee('Laptop');
        $response->assertSee('LP-TEFA-001');
    }

    public function test_user_can_filter_katalog_by_category(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $response = $this->actingAs($user)->get(route('assets.index', ['category' => 'HP']));

        $response->assertStatus(200);
        $response->assertSee('Samsung Galaxy A54');
        $response->assertSee('HP-TEFA-001');
    }

    public function test_user_can_view_borrow_form_for_available_asset(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();
        $this->assertNotNull($asset);
        $this->assertTrue($asset->isAvailable());

        $response = $this->actingAs($user)->get(route('assets.borrow', $asset));

        $response->assertStatus(200);
        $response->assertSee($asset->name);
        $response->assertSee($asset->asset_code);
    }

    public function test_user_cannot_view_borrow_form_for_unavailable_asset(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $asset = Asset::create([
            'asset_category_id' => AssetCategory::first()->id,
            'asset_code' => 'AST-TEST-UNAVAIL',
            'name' => 'Laptop Rusak Test',
            'condition' => AssetCondition::RusakBerat,
            'availability_status' => AssetAvailabilityStatus::TidakTersedia,
        ]);

        $response = $this->actingAs($user)->get(route('assets.borrow', $asset));

        $response->assertStatus(404);
    }

    public function test_user_can_instant_borrow_asset_with_h_plus_3_and_status_borrowed(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('siswa');

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();
        $this->assertNotNull($asset);

        // Simulasi pengiriman foto Base64 hasil tangkapan webcam
        $base64Image = 'data:image/jpeg;base64,' . base64_encode('fake image binary content');

        $response = $this->actingAs($user)->post(route('assets.borrow.store', $asset), [
            'asset_id' => $asset->id,
            'borrower_note' => 'Praktikum TEFA SMKN 1 Bangsri',
            'borrowing_evidence' => $base64Image,
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success');

        // Status langsung Borrowed
        $this->assertDatabaseHas('borrowings', [
            'borrower_user_id' => $user->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed->value,
            'borrower_note' => 'Praktikum TEFA SMKN 1 Bangsri',
        ]);

        // Status Asset langsung berubah menjadi Dipinjam
        $asset->refresh();
        $this->assertEquals(AssetAvailabilityStatus::Dipinjam, $asset->availability_status);

        // Verify borrowings mine page displays active borrowing
        $mineResponse = $this->actingAs($user)->get(route('borrowings.mine'));
        $mineResponse->assertStatus(200);
        $mineResponse->assertSee($asset->name);
        $mineResponse->assertSee($asset->asset_code);
        $mineResponse->assertSee('Dipinjam');
        $mineResponse->assertSee('Kembalikan Barang');
    }

    public function test_user_can_instant_return_asset_and_reset_status_to_tersedia(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('siswa');

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();

        // Buat borrowing dengan status borrowed
        $borrowing = Borrowing::create([
            'borrower_user_id' => $user->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Sedang dipinjam',
        ]);
        $asset->update(['availability_status' => AssetAvailabilityStatus::Dipinjam]);

        $gdImage = imagecreatetruecolor(100, 100);
        ob_start();
        imagejpeg($gdImage);
        $jpegData = ob_get_clean();
        imagedestroy($gdImage);
        $base64ReturnPhoto = 'data:image/jpeg;base64,' . base64_encode($jpegData);

        // Student submits return with webcam photo
        $response = $this->actingAs($user)->post(route('borrowings.return-request', $borrowing), [
            'return_evidence' => $base64ReturnPhoto,
            'return_note' => 'Alat dikembalikan lengkap dan normal',
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success');

        $borrowing->refresh();
        $asset->refresh();

        // Status borrowing langsung Returned (Selesai)
        $this->assertEquals(BorrowingStatus::Returned, $borrowing->status);
        $this->assertNotNull($borrowing->returned_at);
        $this->assertNotNull($borrowing->return_evidence_path);

        // Status asset langsung kembali Tersedia
        $this->assertEquals(AssetAvailabilityStatus::Tersedia, $asset->availability_status);
    }

    public function test_guru_can_borrow_asset_using_route_parameter_without_asset_id_in_body(): void
    {
        Storage::fake('public');

        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();
        $this->assertNotNull($asset);

        $base64Image = 'data:image/jpeg;base64,' . base64_encode('fake guru image binary content');

        // Submit tanpa asset_id di body — harus otomatis terikat via route model {asset}
        $response = $this->actingAs($guru)->post(route('assets.borrow.store', $asset), [
            'borrower_note' => 'Peminjaman untuk keperluan mengajar di Lab TEFA',
            'borrowing_evidence' => $base64Image,
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success', 'Peminjaman berhasil diajukan!');

        $this->assertDatabaseHas('borrowings', [
            'borrower_user_id' => $guru->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed->value,
            'borrower_note' => 'Peminjaman untuk keperluan mengajar di Lab TEFA',
        ]);
    }

    public function test_admin_cannot_access_borrow_form_or_submit_borrowing(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();

        // Admin diblokir dari form peminjaman karena role:siswa|guru
        $this->actingAs($admin)
            ->get(route('assets.borrow', $asset))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->post(route('assets.borrow.store', $asset), [
                'borrower_note' => 'Admin mencoba pinjam',
            ])
            ->assertStatus(403);
    }
}
