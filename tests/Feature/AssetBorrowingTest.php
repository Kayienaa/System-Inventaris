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

    protected function createSiswa(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('siswa');
        \App\Models\SiswaProfile::create([
            'user_id' => $user->id,
            'nis' => 'S-' . $user->id,
            'phone' => '6281234567890',
        ]);

        return $user;
    }

    protected function createGuru(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('guru');
        \App\Models\GuruProfile::create([
            'user_id' => $user->id,
            'nip' => 'G-' . $user->id,
            'phone' => '6281234567891',
        ]);

        return $user;
    }

    protected function createAdmin(array $attributes = []): User
    {
        $admin = User::factory()->create($attributes);
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_user_can_view_katalog_page(): void
    {
        $user = $this->createSiswa();

        $response = $this->actingAs($user)->get(route('assets.index'));

        $response->assertStatus(200);
        $response->assertSee('Laptop');
        $response->assertSee('LP-TEFA-005');
    }

    public function test_user_can_filter_katalog_by_category(): void
    {
        $user = $this->createSiswa();

        $response = $this->actingAs($user)->get(route('assets.index', ['category' => 'HP']));

        $response->assertStatus(200);
        $response->assertSee('Samsung Galaxy A54');
        $response->assertSee('HP-TEFA-001');
    }

    public function test_user_can_view_borrow_form_for_available_asset(): void
    {
        $user = $this->createSiswa();

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
        $user = $this->createSiswa();

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

    public function test_dual_step_borrowing_and_physical_handover_flow(): void
    {
        Storage::fake('public');

        $user = $this->createSiswa();
        $admin = $this->createAdmin();

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();
        $this->assertNotNull($asset);

        // Tahap 1: Siswa mengajukan permohonan pinjam
        $response = $this->actingAs($user)->post(route('assets.borrow.store', $asset), [
            'asset_id' => $asset->id,
            'borrower_note' => 'Praktikum TEFA SMKN 1 Bangsri',
            'due_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success');

        // Peminjaman dibuat dengan status Pending & Asset berstatus Dipesan
        $this->assertDatabaseHas('borrowings', [
            'borrower_user_id' => $user->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending->value,
            'borrower_note' => 'Praktikum TEFA SMKN 1 Bangsri',
        ]);

        $asset->refresh();
        $this->assertEquals(AssetAvailabilityStatus::Dipesan, $asset->availability_status);

        $borrowing = Borrowing::where('borrower_user_id', $user->id)->first();

        // Tahap 2: Admin menyetujui permohonan
        $approveResponse = $this->actingAs($admin)->post(route('admin.borrowings.approve', $borrowing));
        $approveResponse->assertSessionHas('success');

        $borrowing->refresh();
        $this->assertEquals(BorrowingStatus::Approved, $borrowing->status);

        // Tahap 3: Siswa & Admin melakukan serah terima fisik dengan foto kamera
        $base64CheckoutPhoto = $this->createTestBase64Image();

        $checkoutResponse = $this->actingAs($user)->post(route('borrowings.checkout', $borrowing), [
            'borrowing_evidence' => $base64CheckoutPhoto,
        ]);

        $checkoutResponse->assertRedirect(route('borrowings.mine'));
        $checkoutResponse->assertSessionHas('success');

        $borrowing->refresh();
        $asset->refresh();

        // Status borrowing resmi Dipinjam & Asset status Dipinjam
        $this->assertEquals(BorrowingStatus::Borrowed, $borrowing->status);
        $this->assertNotNull($borrowing->borrowed_at);
        $this->assertNotNull($borrowing->borrowing_evidence_path);
        $this->assertEquals(AssetAvailabilityStatus::Dipinjam, $asset->availability_status);
    }

    public function test_dual_step_return_and_physical_verification_flow(): void
    {
        Storage::fake('public');

        $user = $this->createSiswa();
        $admin = $this->createAdmin();

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();

        // Buat borrowing yang sedang berstatus Borrowed
        $borrowing = Borrowing::create([
            'borrower_user_id' => $user->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
            'borrower_note' => 'Sedang dipinjam',
        ]);
        $asset->update(['availability_status' => AssetAvailabilityStatus::Dipinjam]);

        $base64ReturnPhoto = $this->createTestBase64Image();

        // Tahap 1 Pengembalian: Siswa submit pengembalian dengan foto
        $response = $this->actingAs($user)->post(route('borrowings.return-request', $borrowing), [
            'return_evidence' => $base64ReturnPhoto,
            'return_note' => 'Alat dikembalikan lengkap dan normal',
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success');

        $borrowing->refresh();
        // Status menjadi ReturnPendingVerification
        $this->assertEquals(BorrowingStatus::ReturnPendingVerification, $borrowing->status);
        $this->assertNotNull($borrowing->return_evidence_path);

        // Tahap 2 Pengembalian: Admin memverifikasi fisik unit
        $verifyResponse = $this->actingAs($admin)->post(route('admin.borrowings.verify-return', $borrowing), [
            'return_condition' => 'Baik',
            'return_verification_note' => 'Unit lengkap dan normal',
        ]);

        $verifyResponse->assertSessionHas('success');

        $borrowing->refresh();
        $asset->refresh();

        // Status selesai Returned & Asset kembali Tersedia
        $this->assertEquals(BorrowingStatus::Returned, $borrowing->status);
        $this->assertNotNull($borrowing->returned_at);
        $this->assertEquals($admin->id, $borrowing->return_verified_by_user_id);
        $this->assertEquals(AssetAvailabilityStatus::Tersedia, $asset->availability_status);
    }

    public function test_guru_can_borrow_asset_using_route_parameter_without_asset_id_in_body(): void
    {
        Storage::fake('public');

        $guru = $this->createGuru();

        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->first();
        $this->assertNotNull($asset);

        // Submit tanpa asset_id di body — otomatis terikat via route model {asset}
        $response = $this->actingAs($guru)->post(route('assets.borrow.store', $asset), [
            'borrower_note' => 'Peminjaman untuk keperluan mengajar di Lab TEFA',
            'due_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('borrowings.mine'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('borrowings', [
            'borrower_user_id' => $guru->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Pending->value,
            'borrower_note' => 'Peminjaman untuk keperluan mengajar di Lab TEFA',
        ]);
    }

    public function test_admin_cannot_access_borrow_form_or_submit_borrowing(): void
    {
        $admin = $this->createAdmin();

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

    public function test_submit_return_rejects_path_traversal(): void
    {
        $siswa = $this->createSiswa();

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Untuk praktikum',
        ]);

        $response = $this->actingAs($siswa)->postJson("/api/borrowings/{$borrowing->id}/submit-return", [
            'return_evidence_path' => '../../.env',
        ]);

        $response->assertStatus(422);
    }

    public function test_submit_return_rejects_nonexistent_file(): void
    {
        $siswa = $this->createSiswa();

        $asset = Asset::first();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now(),
            'borrowed_at' => now(),
            'due_at' => now()->addDays(3),
            'borrower_note' => 'Untuk praktikum',
        ]);

        $response = $this->actingAs($siswa)->postJson("/api/borrowings/{$borrowing->id}/submit-return", [
            'return_evidence_path' => 'return-evidence/file_palsu_tidak_ada.jpg',
        ]);

        $response->assertStatus(422);
    }
}
