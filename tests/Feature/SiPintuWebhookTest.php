<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiPintuWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        config([
            'services.sipintu.client_secret' => 'test_webhook_secret_key_123',
            'sipintu.client_secret'          => 'test_webhook_secret_key_123',
            'services.sipintu.webhook_tolerance' => 300,
        ]);
    }

    private function generateValidSignature(string $content, ?string $secret = null): string
    {
        $secret = $secret ?? (config('services.sipintu.client_secret') ?: config('sipintu.client_secret'));

        return hash_hmac('sha256', $content, $secret);
    }

    private function postSigned(string $uri, array $payload, array $headers = []): \Illuminate\Testing\TestResponse
    {
        $content = json_encode($payload);
        $signature = $this->generateValidSignature($content);

        return $this->call(
            'POST',
            $uri,
            [],
            [],
            [],
            array_merge([
                'HTTP_X-SiPintu-Signature' => $signature,
                'CONTENT_TYPE'             => 'application/json',
            ], $headers),
            $content
        );
    }

    public function test_request_without_signature_header_returns_401(): void
    {
        $payload = [
            'timestamp' => time(),
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
            'timestamp' => time(),
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

    public function test_fail_closed_when_secret_is_empty_returns_503(): void
    {
        config([
            'services.sipintu.client_secret' => null,
            'sipintu.client_secret'          => null,
        ]);

        $payload = [
            'timestamp' => time(),
            'user' => [
                'name' => 'Fail Closed Test',
                'email' => 'failclosed@smkn1bangsri.sch.id',
                'external_id' => '99911',
            ],
        ];

        $content = json_encode($payload);
        $response = $this->call(
            'POST',
            '/api/sipintu/sync-user',
            [],
            [],
            [],
            [
                'HTTP_X-SiPintu-Signature' => 'some_signature',
                'CONTENT_TYPE'             => 'application/json',
            ],
            $content
        );

        $response->assertStatus(503);
        $response->assertJson([
            'status' => 'error',
            'message' => 'SiPintu client secret is not configured',
        ]);
    }

    public function test_anti_replay_protection_rejects_expired_timestamp(): void
    {
        $payload = [
            'timestamp' => time() - 350, // Melebihi toleransi 300 detik
            'user' => [
                'name' => 'Expired User',
                'email' => 'expired@smkn1bangsri.sch.id',
                'external_id' => '12399',
                'role' => 'siswa',
            ],
        ];

        $response = $this->postSigned('/api/sipintu/sync-user', $payload);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Payload timestamp expired or invalid',
        ]);
    }

    public function test_event_deduplication_returns_duplicate_status_on_repeated_requests(): void
    {
        $payload = [
            'event_id'  => 'evt_' . Str::random(12),
            'timestamp' => time(),
            'user' => [
                'name' => 'Dedup Test User',
                'email' => 'dedup@smkn1bangsri.sch.id',
                'external_id' => '77788',
                'role' => 'siswa',
            ],
        ];

        // Request pertama berhasil
        $firstResponse = $this->postSigned('/api/sipintu/sync-user', $payload);
        $firstResponse->assertOk();
        $firstResponse->assertJson([
            'status' => 'success',
            'action' => 'created',
        ]);

        // Request kedua dengan event_id yang sama ditolak sebagai duplikat
        $secondResponse = $this->postSigned('/api/sipintu/sync-user', $payload);
        $secondResponse->assertOk();
        $secondResponse->assertJson([
            'status' => 'duplicate',
        ]);
    }

    public function test_request_with_valid_signature_syncs_successfully(): void
    {
        $payload = [
            'timestamp' => time(),
            'event_id'  => 'evt_sync_success_' . Str::random(8),
            'user' => [
                'name' => 'Siti Webhook',
                'email' => 'siti.webhook@smkn1bangsri.sch.id',
                'external_id' => '67890',
                'role' => 'siswa',
                'classroom' => 'XI RPL 2',
                'phone' => '081234567899',
            ],
        ];

        $response = $this->postSigned('/api/sipintu/sync-user', $payload);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'action' => 'created',
        ]);

        $user = User::where('sipintu_external_id', '67890')->first();
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

    public function test_webhook_does_not_overwrite_existing_user_password_and_rotates_remember_token(): void
    {
        $initialPasswordHash = Hash::make('original_secure_password');
        $oldRememberToken = 'original_token_value_12345';

        $existingUser = User::create([
            'name'                => 'Existing Teacher',
            'email'               => 'existing.teacher@smkn1bangsri.sch.id',
            'sipintu_external_id' => '198501012010011005',
            'password'            => $initialPasswordHash,
            'remember_token'      => $oldRememberToken,
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $existingUser->assignRole('guru');

        GuruProfile::create([
            'user_id' => $existingUser->id,
            'nip'     => '198501012010011005',
            'phone'   => '085200001111',
        ]);

        $payload = [
            'timestamp' => time(),
            'event_id'  => 'evt_pwd_rot_' . Str::random(8),
            'changed_fields' => ['password'],
            'user' => [
                'name'          => 'Updated Teacher Name',
                'email'         => 'existing.teacher@smkn1bangsri.sch.id',
                'external_id'   => '198501012010011005',
                'role'          => 'guru',
                'password'      => '$2y$12$someNewRemoteHashThatShouldNotBeAppliedDirectly',
            ],
        ];

        $response = $this->postSigned('/api/sipintu/sync-user', $payload);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'action' => 'updated',
        ]);

        $existingUser->refresh();
        $this->assertEquals('Updated Teacher Name', $existingUser->name);
        // Password hash tetap identik dengan password asli lokal SITEFA
        $this->assertEquals($initialPasswordHash, $existingUser->password);
        $this->assertTrue(Hash::check('original_secure_password', $existingUser->password));
        // remember_token telah dirotasi
        $this->assertNotEquals($oldRememberToken, $existingUser->remember_token);
        $this->assertNotEmpty($existingUser->remember_token);
    }

    public function test_webhook_cannot_overwrite_admin_or_super_admin_account(): void
    {
        $admin = User::create([
            'name'              => 'Admin Utama',
            'email'             => 'admin.utama@smkn1bangsri.sch.id',
            'password'          => Hash::make('admin_password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $admin->assignRole('admin');

        $payload = [
            'timestamp' => time(),
            'event_id'  => 'evt_admin_hijack_' . Str::random(8),
            'user' => [
                'name'        => 'Hijacked Admin Name',
                'email'       => 'admin.utama@smkn1bangsri.sch.id',
                'external_id' => '99999',
                'role'        => 'siswa',
            ],
        ];

        $response = $this->postSigned('/api/sipintu/sync-user', $payload);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Cannot overwrite administrative accounts',
        ]);

        $admin->refresh();
        $this->assertEquals('Admin Utama', $admin->name);
    }

    public function test_webhook_cannot_modify_admin_account_via_external_id(): void
    {
        $superAdmin = User::create([
            'name'                => 'Super Admin Account',
            'email'               => 'superadmin@smkn1bangsri.sch.id',
            'sipintu_external_id' => 'admin_ext_99',
            'password'            => Hash::make('superadmin_password'),
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $superAdmin->assignRole('super_admin');

        $payload = [
            'timestamp' => time(),
            'event_id'  => 'evt_admin_ext_hijack_' . Str::random(8),
            'user' => [
                'name'        => 'Changed Name Attack',
                'email'       => 'newemail@smkn1bangsri.sch.id',
                'external_id' => 'admin_ext_99',
                'role'        => 'siswa',
            ],
        ];

        $response = $this->postSigned('/api/sipintu/sync-user', $payload);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Cannot overwrite administrative accounts',
        ]);

        $superAdmin->refresh();
        $this->assertEquals('Super Admin Account', $superAdmin->name);
    }

    public function test_get_webhook_sync_user_returns_ready_status(): void
    {
        $response = $this->getJson('/api/sipintu/sync-user');

        $response->assertOk();
        $response->assertJson([
            'status' => 'ok',
            'message' => 'SiPintu webhook sync-user ready',
        ]);

        // H2: Webhook eksternal tidak didaftarkan di web.php untuk mencegah ambiguitas routing
        $this->getJson('/sipintu/sync-user')->assertNotFound();
    }

    public function test_sync_password_endpoint_acknowledges_requests_and_rotates_token(): void
    {
        $getResponse = $this->getJson('/api/sipintu/sync-password');
        $getResponse->assertOk();
        $getResponse->assertJson([
            'status' => 'ok',
            'message' => 'SiPintu password sync is acknowledged',
        ]);

        // H2: Webhook eksternal tidak didaftarkan di web.php
        $this->getJson('/sipintu/sync-password')->assertNotFound();

        // User setup
        $initialPasswordHash = Hash::make('my_local_pass');
        $oldToken = 'old_remember_token_abc';
        $user = User::create([
            'name'                => 'Student Sync Password',
            'email'               => 'student.sync@smkn1bangsri.sch.id',
            'sipintu_external_id' => 'ext_sync_pass_1',
            'password'            => $initialPasswordHash,
            'remember_token'      => $oldToken,
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $user->assignRole('siswa');

        $payload = [
            'timestamp'     => time(),
            'event_id'      => 'evt_pwd_ack_' . Str::random(8),
            'external_id'   => 'ext_sync_pass_1',
            'password_hash' => '$2y$12$someBcryptHashRemoteFromGateway1234567890123456789012',
        ];

        $postResponse = $this->postSigned('/api/sipintu/sync-password', $payload);
        $postResponse->assertOk();
        $postResponse->assertJson([
            'status' => 'ok',
            'message' => 'SiPintu password sync is acknowledged',
        ]);

        $user->refresh();
        // Password lokal tetap tidak ditimpa jika accept_password_hash bernilai false
        $this->assertEquals($initialPasswordHash, $user->password);
        // remember_token telah dirotasi
        $this->assertNotEquals($oldToken, $user->remember_token);
    }

    public function test_sync_password_accepts_bcrypt_hash_when_configured(): void
    {
        config(['services.sipintu.accept_password_hash' => true]);

        $initialPasswordHash = Hash::make('my_local_pass');
        $user = User::create([
            'name'                => 'Student Hash Accept',
            'email'               => 'student.hash@smkn1bangsri.sch.id',
            'sipintu_external_id' => 'ext_hash_accept_1',
            'password'            => $initialPasswordHash,
            'remember_token'      => 'initial_token_xyz',
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $user->assignRole('siswa');

        // Valid 60-character Bcrypt hash
        $newRemoteHash = Hash::make('new_remote_secret_password');

        $payload = [
            'timestamp'     => time(),
            'event_id'      => 'evt_pwd_accept_' . Str::random(8),
            'external_id'   => 'ext_hash_accept_1',
            'password_hash' => $newRemoteHash,
        ];

        $postResponse = $this->postSigned('/api/sipintu/sync-password', $payload);
        $postResponse->assertOk();

        $user->refresh();
        $this->assertEquals($newRemoteHash, $user->password);
        $this->assertNotEquals('initial_token_xyz', $user->remember_token);
    }
}
