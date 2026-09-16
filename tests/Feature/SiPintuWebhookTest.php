<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiPintuWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function generateValidSignature(string $content): string
    {
        $secret = config('sipintu.client_secret');

        return hash_hmac('sha256', $content, $secret);
    }

    public function test_request_without_signature_header_returns_401(): void
    {
        $payload = [
            'user' => [
                'name' => 'Budi Webhook',
                'email' => 'budi.webhook@smkn1bangsri.sch.id',
                'external_id' => '12345',
                'role' => 'siswa',
            ],
        ];

        $response = $this->postJson('/api/sipintu/sync-user', $payload);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid signature',
        ]);
    }

    public function test_request_with_invalid_signature_returns_401(): void
    {
        $payload = [
            'user' => [
                'name' => 'Budi Webhook',
                'email' => 'budi.webhook@smkn1bangsri.sch.id',
                'external_id' => '12345',
                'role' => 'siswa',
            ],
        ];

        $response = $this->withHeaders([
            'X-SiPintu-Signature' => 'invalid_signature_hash',
        ])->postJson('/api/sipintu/sync-user', $payload);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid signature',
        ]);
    }

    public function test_request_with_valid_signature_syncs_successfully(): void
    {
        $payload = [
            'user' => [
                'name' => 'Siti Webhook',
                'email' => 'siti.webhook@smkn1bangsri.sch.id',
                'external_id' => '67890',
                'role' => 'siswa',
                'classroom' => 'XI RPL 2',
                'phone' => '081234567899',
            ],
        ];

        $content = json_encode($payload);
        $signature = $this->generateValidSignature($content);

        $response = $this->call(
            'POST',
            '/api/sipintu/sync-user',
            [],
            [],
            [],
            [
                'HTTP_X-SiPintu-Signature' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $content
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'action' => 'created',
        ]);

        $user = User::where('email', 'siti.webhook@smkn1bangsri.sch.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Siti Webhook', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasRole('siswa'));

        $profile = SiswaProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('67890', $profile->nis);
        $this->assertEquals('XI RPL 2', $profile->class_name);
        $this->assertEquals('081234567899', $profile->phone);
    }

    public function test_webhook_does_not_overwrite_existing_user_password(): void
    {
        $initialPasswordHash = Hash::make('original_secure_password');

        $existingUser = User::create([
            'name' => 'Existing User',
            'email' => 'existing.user@smkn1bangsri.sch.id',
            'password' => $initialPasswordHash,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $existingUser->assignRole('guru');

        GuruProfile::create([
            'user_id' => $existingUser->id,
            'nip' => '198501012010011005',
            'phone' => '085200001111',
        ]);

        $payload = [
            'user' => [
                'name' => 'Updated Teacher Name',
                'email' => 'existing.user@smkn1bangsri.sch.id',
                'external_id' => '198501012010011005',
                'role' => 'guru',
                'password' => '$2y$12$someNewRemoteHashThatShouldNotBeAppliedDirectly',
            ],
        ];

        $content = json_encode($payload);
        $signature = $this->generateValidSignature($content);

        $response = $this->call(
            'POST',
            '/api/sipintu/sync-user',
            [],
            [],
            [],
            [
                'HTTP_X-SiPintu-Signature' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $content
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'action' => 'updated',
        ]);

        $existingUser->refresh();
        $this->assertEquals('Updated Teacher Name', $existingUser->name);
        // Password hash must remain exactly identical to the initial hash
        $this->assertEquals($initialPasswordHash, $existingUser->password);
        $this->assertTrue(Hash::check('original_secure_password', $existingUser->password));
    }

    public function test_webhook_cannot_overwrite_admin_or_super_admin_account(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin.utama@smkn1bangsri.sch.id',
            'password' => Hash::make('admin_password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $payload = [
            'user' => [
                'name' => 'Hijacked Admin Name',
                'email' => 'admin.utama@smkn1bangsri.sch.id',
                'external_id' => '99999',
                'role' => 'siswa',
            ],
        ];

        $content = json_encode($payload);
        $signature = $this->generateValidSignature($content);

        $response = $this->call(
            'POST',
            '/api/sipintu/sync-user',
            [],
            [],
            [],
            [
                'HTTP_X-SiPintu-Signature' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $content
        );

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Cannot overwrite administrative accounts',
        ]);

        $admin->refresh();
        $this->assertEquals('Admin Utama', $admin->name);
    }
}
