<?php

namespace Tests\Feature;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\BorrowingStatus;
use App\Models\AdminNotification;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\AssetCategorySeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AssetCategorySeeder::class);
        $this->seed(AssetSeeder::class);
    }

    protected function createAdmin(string $role = 'admin'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    protected function createSiswa(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $user->id,
            'nis' => '123456',
            'class_name' => 'XII RPL 1',
            'phone' => '6281234567890',
        ]);
        return $user;
    }

    protected function createGuru(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('guru');
        GuruProfile::create([
            'user_id' => $user->id,
            'nip' => '198001012005011001',
            'phone' => '6281234567891',
        ]);
        return $user;
    }

    public function test_admin_can_fetch_notifications_via_json_endpoint(): void
    {
        $admin = $this->createAdmin();

        AdminNotification::create([
            'type' => 'borrow_requested',
            'title' => 'Peminjaman Baru Masuk',
            'message' => 'Budi Santoso (XII RPL 1) mengajukan peminjaman unit Laptop Dell.',
            'data' => [
                'borrower_name' => 'Budi Santoso',
                'borrower_role' => 'XII RPL 1',
                'asset_name' => 'Laptop Dell',
                'asset_code' => 'LP-001',
                'time' => now()->toIso8601String(),
                'borrowing_id' => 1,
            ],
            'is_read' => false,
        ]);

        AdminNotification::create([
            'type' => 'return_submitted',
            'title' => 'Pengembalian Unit Masuk',
            'message' => 'Budi Santoso telah menyerahkan kembali unit Laptop Dell.',
            'data' => [
                'borrower_name' => 'Budi Santoso',
                'borrower_role' => 'XII RPL 1',
                'asset_name' => 'Laptop Dell',
                'asset_code' => 'LP-001',
                'time' => now()->toIso8601String(),
                'borrowing_id' => 1,
            ],
            'is_read' => true,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.notifications.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications' => [
                '*' => [
                    'id',
                    'type',
                    'title',
                    'message',
                    'data',
                    'is_read',
                    'created_at_human',
                ],
            ],
            'unread_count',
        ]);

        $response->assertJson([
            'unread_count' => 1,
        ]);

        $this->assertCount(2, $response->json('notifications'));
    }

    public function test_borrowing_request_automatically_creates_admin_notification(): void
    {
        $siswa = $this->createSiswa(['name' => 'Ahmad Dahlan']);
        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->firstOrFail();

        $response = $this->actingAs($siswa)->post(route('assets.borrow.store', $asset), [
            'asset_id' => $asset->id,
            'borrower_note' => 'Keperluan ujian praktik',
            'due_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'borrow_requested',
            'title' => 'Peminjaman Baru Masuk',
            'is_read' => false,
        ]);

        $notification = AdminNotification::where('type', 'borrow_requested')->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Ahmad Dahlan', $notification->message);
        $this->assertStringContainsString($asset->name, $notification->message);
        $this->assertStringContainsString($asset->asset_code, $notification->message);
        $this->assertEquals($asset->name, $notification->data['asset_name']);
        $this->assertEquals($asset->asset_code, $notification->data['asset_code']);
    }

    public function test_return_submission_automatically_creates_admin_notification(): void
    {
        Storage::fake('public');

        $siswa = $this->createSiswa(['name' => 'Siti Nurhaliza']);
        $asset = Asset::where('availability_status', AssetAvailabilityStatus::Tersedia)->firstOrFail();

        $borrowing = Borrowing::create([
            'borrower_user_id' => $siswa->id,
            'asset_id' => $asset->id,
            'status' => BorrowingStatus::Borrowed,
            'requested_at' => now()->subDay(),
            'borrowed_at' => now()->subDay(),
            'due_at' => now()->addDays(2),
            'borrower_note' => 'Dipinjam untuk tugas',
        ]);
        $asset->update(['availability_status' => AssetAvailabilityStatus::Dipinjam]);

        $base64Image = $this->createTestBase64Image();

        $response = $this->actingAs($siswa)->post(route('borrowings.return-request', $borrowing), [
            'return_evidence' => $base64Image,
            'return_note' => 'Sudah selesai digunakan',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admin_notifications', [
            'type' => 'return_submitted',
            'title' => 'Pengembalian Unit Masuk',
            'is_read' => false,
        ]);

        $notification = AdminNotification::where('type', 'return_submitted')->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Siti Nurhaliza', $notification->message);
        $this->assertStringContainsString($asset->name, $notification->message);
        $this->assertStringContainsString($asset->asset_code, $notification->message);
        $this->assertEquals($asset->name, $notification->data['asset_name']);
        $this->assertEquals($asset->asset_code, $notification->data['asset_code']);
        $this->assertEquals($borrowing->id, $notification->data['borrowing_id']);
    }

    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        $admin = $this->createAdmin();

        AdminNotification::create([
            'type' => 'borrow_requested',
            'title' => 'Peminjaman 1',
            'message' => 'Pesan 1',
            'is_read' => false,
        ]);
        AdminNotification::create([
            'type' => 'borrow_requested',
            'title' => 'Peminjaman 2',
            'message' => 'Pesan 2',
            'is_read' => false,
        ]);

        $this->assertEquals(2, AdminNotification::unread()->count());

        $response = $this->actingAs($admin)->postJson(route('admin.notifications.mark-all-read'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, AdminNotification::unread()->count());
    }

    public function test_admin_can_destroy_all_notifications(): void
    {
        $admin = $this->createAdmin();

        AdminNotification::create([
            'type' => 'borrow_requested',
            'title' => 'Peminjaman 1',
            'message' => 'Pesan 1',
            'is_read' => false,
        ]);
        AdminNotification::create([
            'type' => 'return_submitted',
            'title' => 'Pengembalian 1',
            'message' => 'Pesan 2',
            'is_read' => true,
        ]);

        $this->assertEquals(2, AdminNotification::count());

        $response = $this->actingAs($admin)->deleteJson(route('admin.notifications.destroy-all'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, AdminNotification::count());
    }

    public function test_non_admin_users_are_forbidden_from_accessing_admin_notifications(): void
    {
        $siswa = $this->createSiswa();
        $guru = $this->createGuru();

        // Siswa access test
        $this->actingAs($siswa)->getJson(route('admin.notifications.index'))
            ->assertStatus(403);

        $this->actingAs($siswa)->postJson(route('admin.notifications.mark-all-read'))
            ->assertStatus(403);

        $this->actingAs($siswa)->deleteJson(route('admin.notifications.destroy-all'))
            ->assertStatus(403);

        // Guru access test
        $this->actingAs($guru)->getJson(route('admin.notifications.index'))
            ->assertStatus(403);

        $this->actingAs($guru)->postJson(route('admin.notifications.mark-all-read'))
            ->assertStatus(403);

        $this->actingAs($guru)->deleteJson(route('admin.notifications.destroy-all'))
            ->assertStatus(403);
    }
}
