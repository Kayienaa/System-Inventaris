<?php

namespace Tests\Feature\Auth;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Email Siswa / NIP Guru');
        $response->assertSee('isi email disini');
        $response->assertSee('isi sandi disini');
    }

    public function test_admin_donny_can_authenticate_using_email_and_default_password(): void
    {
        $admin = User::create([
            'name' => 'Mas Donny (Admin TEFA)',
            'email' => 'admindonny@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admindonny@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_super_admin_can_authenticate_using_email_and_default_password(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'AdminInventaris@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'AdminInventaris@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_guru_can_authenticate_using_nip_and_default_password(): void
    {
        $guru = User::create([
            'name' => 'Guru Pengajar',
            'email' => 'guru@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '199301162022211000',
            'phone' => '081234567890',
        ]);

        $response = $this->post('/login', [
            'email' => '199301162022211000',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($guru);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_guru_can_authenticate_using_email_and_default_password(): void
    {
        $guru = User::create([
            'name' => 'Guru Pengajar',
            'email' => '199301162022211000@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => '199301162022211000@smkn1bangsri.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($guru);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_siswa_can_not_authenticate_using_nis_numeric(): void
    {
        $siswa = User::create([
            'name' => 'Siswa Penguji',
            'email' => '4710@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '4710',
            'nisn' => '0051234710',
            'class_name' => 'XII RPL 1',
        ]);

        $response = $this->post('/login', [
            'email' => '4710',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);
    }

    public function test_siswa_can_authenticate_using_official_school_email_and_default_password(): void
    {
        $siswa = User::create([
            'name' => 'Siswa Penguji',
            'email' => '4710@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => '4710@smkn1bangsri.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($siswa);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);
    }

    public function test_guru_can_not_authenticate_with_invalid_password_via_nip(): void
    {
        $guru = User::create([
            'name' => 'Guru Pengajar',
            'email' => 'guru@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '199301162022211000',
        ]);

        $response = $this->post('/login', [
            'email' => '199301162022211000',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);
    }

    public function test_siswa_can_not_authenticate_with_invalid_password_via_email(): void
    {
        $siswa = User::create([
            'name' => 'Siswa Penguji',
            'email' => '4710@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => '4710@smkn1bangsri.sch.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);
    }

    public function test_can_not_authenticate_with_unregistered_numeric_identity(): void
    {
        $response = $this->post('/login', [
            'email' => '9999999999',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_non_admin_can_authenticate_via_sipintu_credentials_fallback_when_local_password_is_outdated(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        $siswa = User::create([
            'name'                => 'Budi Santoso',
            'email'               => 'budi.santoso@smkn1bangsri.sch.id',
            'sipintu_external_id' => '212210001',
            'password'            => Hash::make('old_local_pass'),
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $siswa->assignRole('siswa');

        Http::fake([
            "{$baseUrl}/api/v1/auth/verify-credentials" => Http::response([
                'success' => true,
                'data' => [
                    'external_id' => '212210001',
                    'name'        => 'Budi Santoso',
                    'email'       => 'budi.santoso@smkn1bangsri.sch.id',
                ],
            ], 200),
        ]);

        $response = $this->post('/login', [
            'email'    => 'budi.santoso@smkn1bangsri.sch.id',
            'password' => 'new_remote_sipintu_password',
        ]);

        $this->assertAuthenticatedAs($siswa);
        $response->assertRedirect(route('dashboard', absolute: false));

        // Password lokal berhasil diperbarui dan disinkronkan ke hash baru
        $siswa->refresh();
        $this->assertTrue(Hash::check('new_remote_sipintu_password', $siswa->password));
    }

    public function test_admin_and_super_admin_never_use_sipintu_fallback(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        $admin = User::create([
            'name'                => 'Site Admin',
            'email'               => 'admin@smkn1bangsri.sch.id',
            'sipintu_external_id' => 'admin_ext_123',
            'password'            => Hash::make('real_admin_pass'),
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $admin->assignRole('admin');

        // SiPintu endpoint disiapkan jika dipanggil
        Http::fake([
            "{$baseUrl}/api/v1/auth/verify-credentials" => Http::response([
                'success' => true,
                'data' => [
                    'external_id' => 'admin_ext_123',
                ],
            ], 200),
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@smkn1bangsri.sch.id',
            'password' => 'wrong_pass',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);

        // Pastikan endpoint verifikasi kredensial SiPintu TIDAK PERNAH dipanggil untuk akun administratif
        Http::assertNothingSent();
    }

    public function test_fallback_fails_if_remote_credentials_are_invalid(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        $guru = User::create([
            'name'                => 'Guru Fail Test',
            'email'               => 'guru.fail@smkn1bangsri.sch.id',
            'sipintu_external_id' => '199001012015011001',
            'password'            => Hash::make('local_secure_pass'),
            'email_verified_at'   => now(),
            'is_active'           => true,
        ]);
        $guru->assignRole('guru');

        Http::fake([
            "{$baseUrl}/api/v1/auth/verify-credentials" => Http::response([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401),
        ]);

        $response = $this->post('/login', [
            'email'    => 'guru.fail@smkn1bangsri.sch.id',
            'password' => 'completely_wrong_pass',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Email, NIS/NIP, atau kata sandi yang Anda masukkan salah.']);
    }
}
