<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OAuthSsoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_oauth_callback_redirects_to_login_if_code_missing(): void
    {
        $response = $this->get('/oauth/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Otorisasi SSO SiPintu gagal: Kode otorisasi tidak ditemukan.');
        $this->assertFalse(Auth::check());
    }

    public function test_oauth_callback_handles_token_exchange_failure(): void
    {
        $baseUrl = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id')), '/');

        Http::fake([
            $baseUrl . '/oauth/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Authorization code expired or invalid.',
            ], 400),
        ]);

        $response = $this->get('/oauth/callback?code=expired_or_invalid_code');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Authorization code expired or invalid.');
        $this->assertFalse(Auth::check());
    }

    public function test_oauth_callback_authenticates_and_provisions_student_user(): void
    {
        $baseUrl = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id')), '/');

        Http::fake([
            $baseUrl . '/oauth/token' => Http::response([
                'access_token' => 'mock_token_student_123',
                'token_type'   => 'Bearer',
                'expires_in'   => 3600,
            ], 200),
            $baseUrl . '/api/v1/user' => Http::response([
                'data' => [
                    'id'          => 42,
                    'external_id' => '212210099',
                    'name'        => 'Budi Siswa Teladan',
                    'email'       => 'budi.sso@smkn1bangsri.sch.id',
                    'role'        => 'student',
                    'classroom'   => 'XII RPL 1',
                    'phone'       => '081234567899',
                    'password'    => password_hash('siswa123', PASSWORD_BCRYPT),
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=valid_auth_code_123');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertTrue(Auth::check());
        $user = Auth::user();
        $this->assertEquals('budi.sso@smkn1bangsri.sch.id', $user->email);
        $this->assertEquals('Budi Siswa Teladan', $user->name);
        $this->assertTrue($user->hasRole('siswa'));

        $profile = SiswaProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('212210099', $profile->nis);
        $this->assertEquals('XII RPL 1', $profile->class_name);
        $this->assertEquals('081234567899', $profile->phone);
    }

    public function test_oauth_callback_authenticates_and_provisions_teacher_user(): void
    {
        $baseUrl = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id')), '/');

        Http::fake([
            $baseUrl . '/oauth/token' => Http::response([
                'access_token' => 'mock_token_teacher_456',
                'token_type'   => 'Bearer',
                'expires_in'   => 3600,
            ], 200),
            $baseUrl . '/api/v1/user' => Http::response([
                'data' => [
                    'id'          => 88,
                    'external_id' => '198501012010011099',
                    'name'        => 'Siti Rahayu, M.Kom',
                    'email'       => 'siti.rahayu@smkn1bangsri.sch.id',
                    'role'        => 'teacher',
                    'phone'       => '081987654321',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=valid_teacher_code_456');

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $user = Auth::user();
        $this->assertEquals('siti.rahayu@smkn1bangsri.sch.id', $user->email);
        $this->assertTrue($user->hasRole('guru'));

        $profile = GuruProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('198501012010011099', $profile->nip);
        $this->assertEquals('081987654321', $profile->phone);
    }

    public function test_oauth_callback_syncs_password_hash_if_provided(): void
    {
        $baseUrl = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id')), '/');

        $oldHash = Hash::make('oldpassword123');
        $newHash = '$2y$12$newhashfromsipintugateway1234567890abcdef1234567890abcdef123';

        $user = User::create([
            'name'              => 'Existing Student',
            'email'             => 'student.existing@smkn1bangsri.sch.id',
            'password'          => $oldHash,
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $user->assignRole('siswa');

        Http::fake([
            $baseUrl . '/oauth/token' => Http::response([
                'access_token' => 'mock_token_sync_pwd',
            ], 200),
            $baseUrl . '/api/v1/user' => Http::response([
                'data' => [
                    'id'            => 100,
                    'name'          => 'Existing Student Updated',
                    'email'         => 'student.existing@smkn1bangsri.sch.id',
                    'role'          => 'student',
                    'password_hash' => $newHash,
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=code_pwd');

        $response->assertRedirect('/dashboard');
        $user->refresh();
        $this->assertEquals('Existing Student Updated', $user->name);
        $this->assertEquals($newHash, $user->password);
    }

    public function test_webhook_sync_user_validates_signature(): void
    {
        $payload = [
            'event' => 'user.updated',
            'user'  => [
                'name'  => 'Test User',
                'email' => 'test@smkn1bangsri.sch.id',
            ],
        ];

        // Request with invalid signature
        $response = $this->withHeaders([
            'X-SiPintu-Signature' => 'invalid_sha256_signature',
        ])->postJson('/api/sipintu/sync-user', $payload);

        $response->assertStatus(401);
        $response->assertJson(['status' => 'error', 'message' => 'Invalid signature']);
    }

    public function test_webhook_sync_user_updates_existing_user_data(): void
    {
        $user = User::create([
            'name'              => 'Budi Santoso',
            'email'             => 'budi.lama@smkn1bangsri.sch.id',
            'password'          => Hash::make('passwordlama'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $user->assignRole('siswa');
        SiswaProfile::create([
            'user_id'    => $user->id,
            'nis'        => '12345678',
            'class_name' => 'XI RPL 1',
            'phone'      => '0811111111',
        ]);

        $clientSecret = env('SIPINTU_CLIENT_SECRET', config('services.sipintu.client_secret', 'sec_fPitvBwUAC6PT6cGMNXeUmn50uvWtdri'));

        $newPasswordHash = password_hash('newencryptedpassword', PASSWORD_BCRYPT);
        $payload = [
            'event'     => 'user.updated',
            'event_id'  => 'c76f6b57-d3bb-4d64-9276-88c278fb1234',
            'timestamp' => '2026-09-08T15:00:00+07:00',
            'user'      => [
                'id'            => '10',
                'external_id'   => '12345678',
                'name'          => 'Budi Santoso Baru',
                'email'         => 'budi.baru@smkn1bangsri.sch.id',
                'role'          => 'student',
                'classroom'     => 'XII RPL 1',
                'phone'         => '081234567890',
                'password_hash' => $newPasswordHash,
            ],
            'previous'  => [
                'email' => 'budi.lama@smkn1bangsri.sch.id',
                'name'  => 'Budi Santoso',
            ],
        ];

        $content = json_encode($payload);
        $signature = hash_hmac('sha256', $content, $clientSecret);

        $response = $this->call(
            'POST',
            '/api/sipintu/sync-user',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'          => 'application/json',
                'HTTP_ACCEPT'           => 'application/json',
                'HTTP_X_SIPINTU_SIGNATURE' => $signature,
            ],
            $content
        );

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'action' => 'updated',
        ]);

        $user->refresh();
        $this->assertEquals('Budi Santoso Baru', $user->name);
        $this->assertEquals('budi.baru@smkn1bangsri.sch.id', $user->email);
        $this->assertEquals($newPasswordHash, $user->password);

        $profile = SiswaProfile::where('user_id', $user->id)->first();
        $this->assertEquals('XII RPL 1', $profile->class_name);
        $this->assertEquals('081234567890', $profile->phone);
    }

    public function test_manual_login_continues_to_work_normally(): void
    {
        $user = User::create([
            'name'              => 'Manual Student',
            'email'             => 'manual.student@smkn1bangsri.sch.id',
            'password'          => Hash::make('secret1234'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $user->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $user->id,
            'nis'     => '998877',
            'phone'   => '081234567890',
        ]);

        $response = $this->post('/login', [
            'email'    => 'manual.student@smkn1bangsri.sch.id',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_sipintu_service_supports_positional_arguments_and_client_validation(): void
    {
        $baseUrl = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id')), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response([
                'success' => true,
                'count'   => 2,
                'data'    => [
                    [
                        'id'   => 1,
                        'nis'  => '1001',
                        'nama' => 'Student One',
                    ],
                    [
                        'id'   => 2,
                        'nis'  => '1002',
                        'nama' => 'Student Two',
                    ],
                ],
            ], 200),
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response([
                'success' => true,
                'count'   => 1,
                'data'    => [
                    [
                        'id'   => 10,
                        'nip'  => '19800101',
                        'nama' => 'Guru Hebat',
                    ],
                ],
            ], 200),
            $baseUrl . '/api/v1/validate-client' => Http::response([
                'valid' => true,
                'client_name' => 'System Inventaris',
            ], 200),
        ]);

        $service = app(\App\Services\SiPintuService::class);

        // Uji getStudents dengan positional argument
        $students = $service->getStudents(nis: '1001', limit: 10, forceRefresh: true);
        $this->assertTrue($students['success']);
        $this->assertEquals(1, $students['count']);
        $this->assertEquals('1001', $students['data'][0]['nis']);

        // Uji getTeachers dengan positional argument
        $teachers = $service->getTeachers(nip: '19800101', forceRefresh: true);
        $this->assertTrue($teachers['success']);
        $this->assertEquals(1, $teachers['count']);

        // Uji validateClient
        $validation = $service->validateClient();
        $this->assertTrue($validation['valid']);
        $this->assertEquals('System Inventaris', $validation['client_name']);
    }
}
