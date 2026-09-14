<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OAuthCallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function getBaseUrl(): string
    {
        return rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', config('sipintu.api_url', 'http://localhost:8000'))), '/');
    }

    public function test_callback_without_code_redirects_to_login_with_error(): void
    {
        $response = $this->get('/oauth/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Otorisasi SSO SiPintu gagal: Kode otorisasi tidak ditemukan.');
        $this->assertGuest();
    }

    public function test_existing_user_with_null_email_verified_at_is_auto_verified_and_directed_to_dashboard_when_phone_filled(): void
    {
        $baseUrl = $this->getBaseUrl();

        // Akun lama yang belum terverifikasi email (email_verified_at = null)
        $user = User::create([
            'name' => 'Siswa Lama',
            'email' => 'siswa.lama@smkn1bangsri.sch.id',
            'password' => Hash::make('secret123'),
            'email_verified_at' => null,
            'is_active' => true,
        ]);
        $user->assignRole('siswa');

        SiswaProfile::create([
            'user_id' => $user->id,
            'nis' => '10001',
            'phone' => '081234567890',
            'class_name' => 'XII RPL 1',
        ]);

        Http::fake([
            "{$baseUrl}/oauth/token" => Http::response([
                'access_token' => 'mock_access_token_123',
                'token_type' => 'Bearer',
            ], 200),
            "{$baseUrl}/api/v1/user" => Http::response([
                'data' => [
                    'name' => 'Siswa Lama Terupdate',
                    'email' => 'siswa.lama@smkn1bangsri.sch.id',
                    'external_id' => '10001',
                    'role' => 'siswa',
                    'phone' => '081234567890',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=valid_auth_code_123');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');

        // Pastikan email_verified_at otomatis terisi now()
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        // Akses dashboard: tidak di-redirect ke notice verifikasi email Breeze atau complete-phone
        $dashboardResponse = $this->actingAs($user)->get('/dashboard');
        $dashboardResponse->assertOk();
    }

    public function test_existing_user_with_null_email_verified_at_and_empty_phone_is_auto_verified_and_directed_to_complete_phone(): void
    {
        $baseUrl = $this->getBaseUrl();

        // Akun lama tanpa nomor HP dan email belum terverifikasi
        $user = User::create([
            'name' => 'Siswa Tanpa HP',
            'email' => 'siswa.nohp@smkn1bangsri.sch.id',
            'password' => Hash::make('secret123'),
            'email_verified_at' => null,
            'is_active' => true,
        ]);
        $user->assignRole('siswa');

        SiswaProfile::create([
            'user_id' => $user->id,
            'nis' => '10002',
            'phone' => null,
            'class_name' => 'XI RPL 1',
        ]);

        Http::fake([
            "{$baseUrl}/oauth/token" => Http::response([
                'access_token' => 'mock_access_token_456',
                'token_type' => 'Bearer',
            ], 200),
            "{$baseUrl}/api/v1/user" => Http::response([
                'data' => [
                    'name' => 'Siswa Tanpa HP',
                    'email' => 'siswa.nohp@smkn1bangsri.sch.id',
                    'external_id' => '10002',
                    'role' => 'siswa',
                    'phone' => null,
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=valid_auth_code_456');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');

        // Pastikan email_verified_at otomatis terisi now()
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        // Saat mengakses /dashboard, middleware EnsurePhoneIsFilled mengarahkan ke /complete-phone (bukan notice verify-email)
        $dashboardResponse = $this->actingAs($user)->get('/dashboard');
        $dashboardResponse->assertRedirect(route('complete-phone'));
    }

    public function test_new_user_provisioning_sets_verified_email_and_authenticates(): void
    {
        $baseUrl = $this->getBaseUrl();

        Http::fake([
            "{$baseUrl}/oauth/token" => Http::response([
                'access_token' => 'mock_access_token_789',
                'token_type' => 'Bearer',
            ], 200),
            "{$baseUrl}/api/v1/user" => Http::response([
                'data' => [
                    'name' => 'Guru Baru SSO',
                    'email' => 'guru.baru@smkn1bangsri.sch.id',
                    'external_id' => '199005052020011002',
                    'role' => 'guru',
                    'phone' => '081999888777',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=valid_auth_code_789');

        $user = User::where('email', 'guru.baru@smkn1bangsri.sch.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Guru Baru SSO', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasRole('guru'));

        $profile = GuruProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('199005052020011002', $profile->nip);
        $this->assertEquals('081999888777', $profile->phone);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }
}
