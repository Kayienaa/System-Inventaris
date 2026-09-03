<?php

namespace Tests\Feature\Admin;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use App\Enums\BorrowingStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AuditLog;
use App\Models\Borrowing;
use App\Models\User;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $siswa;
    protected AssetCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            PreventRequestForgery::class,
            ValidateCsrfToken::class,
        ]);

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);

        $this->admin = User::factory()->create(['name' => 'Super Admin']);
        $this->admin->assignRole('admin');

        $this->siswa = User::factory()->create(['name' => 'Siswa Test']);
        $this->siswa->assignRole('siswa');

        $this->category = AssetCategory::first() ?? AssetCategory::factory()->create();
    }

    /**
     * Buat fake image tanpa ketergantungan ekstensi GD.
     */
    protected function createFakeImage(string $filename = 'test.jpg'): UploadedFile
    {
        // Minimal valid 1x1 JPEG binary
        $base64Jpeg = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
        return UploadedFile::fake()->createWithContent($filename, (string) base64_decode($base64Jpeg));
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_assets(): void
    {
        $response = $this->get(route('admin.assets.index'));
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_assets(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('admin.assets.index'));
        $response->assertForbidden();

        $responseCreate = $this->actingAs($this->siswa)->get(route('admin.assets.create'));
        $responseCreate->assertForbidden();
    }

    public function test_admin_can_view_asset_index_with_search_and_filters(): void
    {
        $assetA = Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'TEST-001',
            'name' => 'MacBook Pro M1',
            'brand' => 'Apple',
            'model' => 'A2338',
            'serial_number' => 'C02G1234TEST',
            'condition' => AssetCondition::Baik,
            'availability_status' => AssetAvailabilityStatus::Tersedia,
        ]);

        $assetB = Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'TEST-002',
            'name' => 'Dell Latitude 5420',
            'brand' => 'Dell',
            'model' => '5420',
            'serial_number' => 'SN-DELL-9876',
            'condition' => AssetCondition::RusakRingan,
            'availability_status' => AssetAvailabilityStatus::Perbaikan,
        ]);

        // Akses halaman index
        $response = $this->actingAs($this->admin)->get(route('admin.assets.index'));
        $response->assertOk();
        $response->assertSee('MacBook Pro M1');
        $response->assertSee('Dell Latitude 5420');
        $response->assertSee('TEST-001');
        $response->assertSee('TEST-002');

        // Filter search
        $searchResponse = $this->actingAs($this->admin)->get(route('admin.assets.index', ['search' => 'MacBook']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('MacBook Pro M1');
        $searchResponse->assertDontSee('Dell Latitude 5420');

        // Filter status
        $statusResponse = $this->actingAs($this->admin)->get(route('admin.assets.index', ['availability_status' => 'perbaikan']));
        $statusResponse->assertOk();
        $statusResponse->assertSee('Dell Latitude 5420');
        $statusResponse->assertDontSee('MacBook Pro M1');
    }

    public function test_admin_can_view_create_asset_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.assets.create'));
        $response->assertOk();
        $response->assertSee('Tambah Unit Aset');
        $response->assertSee($this->category->name);
    }

    public function test_admin_can_store_new_asset_with_photo_and_audit_log(): void
    {
        Storage::fake('public');

        $photo = $this->createFakeImage('asset_photo.jpg');

        $payload = [
            'asset_category_id' => $this->category->id,
            'asset_code' => 'NEW-LP-099',
            'serial_number' => 'SN-NEW-998877',
            'name' => 'ThinkPad X1 Carbon Gen 10',
            'brand' => 'Lenovo',
            'model' => '21CB',
            'condition' => AssetCondition::Baik->value,
            'availability_status' => AssetAvailabilityStatus::Tersedia->value,
            'photo' => $photo,
            'notes' => 'Laptop praktikum TEFA',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.assets.store'), $payload);

        $response->assertRedirect(route('admin.assets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assets', [
            'asset_code' => 'NEW-LP-099',
            'serial_number' => 'SN-NEW-998877',
            'name' => 'ThinkPad X1 Carbon Gen 10',
            'brand' => 'Lenovo',
        ]);

        $asset = Asset::where('asset_code', 'NEW-LP-099')->first();
        $this->assertNotNull($asset);
        $this->assertNotNull($asset->photo_path);
        Storage::disk('public')->assertExists($asset->photo_path);

        // Pastikan audit log tercatat
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $this->admin->id,
            'action' => 'asset.created',
            'entity_type' => Asset::class,
            'entity_id' => $asset->id,
        ]);
    }

    public function test_store_asset_validates_uniqueness_and_required_fields(): void
    {
        Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'DUPLICATE-CODE',
            'name' => 'Existing Laptop',
            'brand' => 'HP',
            'serial_number' => 'DUPLICATE-SN',
            'condition' => AssetCondition::Baik,
            'availability_status' => AssetAvailabilityStatus::Tersedia,
        ]);

        $payload = [
            'asset_category_id' => $this->category->id,
            'asset_code' => 'DUPLICATE-CODE',
            'serial_number' => 'DUPLICATE-SN',
            'name' => '',
            'brand' => '',
            'condition' => 'kondisi_tidak_valid',
            'availability_status' => 'status_tidak_valid',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.assets.store'), $payload);

        $response->assertSessionHasErrors([
            'asset_code',
            'serial_number',
            'name',
            'brand',
            'condition',
            'availability_status',
        ]);
    }

    public function test_admin_can_view_edit_asset_form(): void
    {
        $asset = Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'EDIT-001',
            'name' => 'Acer Swift 3',
            'brand' => 'Acer',
            'serial_number' => 'SN-ACER-111',
            'condition' => AssetCondition::Baik,
            'availability_status' => AssetAvailabilityStatus::Tersedia,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.assets.edit', $asset));
        $response->assertOk();
        $response->assertSee('Acer Swift 3');
        $response->assertSee('EDIT-001');
    }

    public function test_admin_can_update_asset_and_replace_photo(): void
    {
        Storage::fake('public');

        // Buat file foto lama
        $oldPhotoPath = 'assets/old_test_photo.jpg';
        Storage::disk('public')->put($oldPhotoPath, 'old content');

        $asset = Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'UPD-001',
            'name' => 'Original Name',
            'brand' => 'Original Brand',
            'model' => 'Old Model',
            'serial_number' => 'SN-UPD-001',
            'condition' => AssetCondition::Baik,
            'availability_status' => AssetAvailabilityStatus::Tersedia,
            'photo_path' => $oldPhotoPath,
        ]);

        $newPhoto = $this->createFakeImage('new_photo.jpg');

        $updatePayload = [
            'asset_category_id' => $this->category->id,
            'asset_code' => 'UPD-001', // tetap sama
            'serial_number' => 'SN-UPD-001', // tetap sama
            'name' => 'Updated Name Laptop',
            'brand' => 'Updated Brand',
            'model' => 'New Model',
            'condition' => AssetCondition::RusakRingan->value,
            'availability_status' => AssetAvailabilityStatus::Perbaikan->value,
            'photo' => $newPhoto,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.assets.update', $asset), $updatePayload);

        $response->assertRedirect(route('admin.assets.index'));
        $response->assertSessionHas('success');

        $asset->refresh();
        $this->assertEquals('Updated Name Laptop', $asset->name);
        $this->assertEquals(AssetCondition::RusakRingan, $asset->condition);
        $this->assertEquals(AssetAvailabilityStatus::Perbaikan, $asset->availability_status);

        // Foto lama harus sudah terhapus dan foto baru tersimpan
        Storage::disk('public')->assertMissing($oldPhotoPath);
        Storage::disk('public')->assertExists($asset->photo_path);

        // Audit log tercatat
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $this->admin->id,
            'action' => 'asset.updated',
            'entity_type' => Asset::class,
            'entity_id' => $asset->id,
        ]);
    }

    public function test_admin_cannot_delete_asset_with_active_borrowing(): void
    {
        $asset = Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'BORROWED-001',
            'name' => 'Active Borrowed Laptop',
            'brand' => 'Asus',
            'serial_number' => 'SN-BORROW-001',
            'condition' => AssetCondition::Baik,
            'availability_status' => AssetAvailabilityStatus::Dipinjam,
        ]);

        // Buat peminjaman aktif
        Borrowing::create([
            'borrower_user_id' => $this->siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.assets.destroy', $asset));

        $response->assertRedirect(route('admin.assets.index'));
        $response->assertSessionHas('error');

        // Asset tidak boleh terhapus
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_delete_asset_when_no_active_borrowing(): void
    {
        $asset = Asset::create([
            'asset_category_id' => $this->category->id,
            'asset_code' => 'FREE-001',
            'name' => 'Free Asset To Delete',
            'brand' => 'HP',
            'serial_number' => 'SN-FREE-001',
            'condition' => AssetCondition::Baik,
            'availability_status' => AssetAvailabilityStatus::Tersedia,
        ]);

        // Peminjaman masa lalu yang sudah berstatus returned
        Borrowing::create([
            'borrower_user_id' => $this->siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Returned,
            'requested_at' => now()->subDays(10),
            'borrowed_at' => now()->subDays(10),
            'due_at' => now()->subDays(5),
            'returned_at' => now()->subDays(7),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.assets.destroy', $asset));

        $response->assertRedirect(route('admin.assets.index'));
        $response->assertSessionHas('success');

        // Asset harus ter-soft delete
        $this->assertSoftDeleted('assets', [
            'id' => $asset->id,
        ]);

        // Audit log tercatat
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $this->admin->id,
            'action' => 'asset.deleted',
            'entity_type' => Asset::class,
            'entity_id' => $asset->id,
        ]);
    }
}
