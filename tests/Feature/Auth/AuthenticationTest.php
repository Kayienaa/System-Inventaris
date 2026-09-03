<?php

namespace Tests\Feature\Auth;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Email / NIS / NIP');
        $response->assertSee('Email SiPintu, NIS, atau NIP (contoh: 199301162022211000)');
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

    public function test_siswa_can_authenticate_using_nis_and_default_password(): void
    {
        $siswa = User::create([
            'name' => 'Siswa Penguji',
            'email' => 'siswa@smkn1bangsri.sch.id',
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

        $this->assertAuthenticatedAs($siswa);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_siswa_can_authenticate_using_sipintu_email_and_default_password(): void
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

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
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

        $this->post('/login', [
            'email' => '199301162022211000',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_siswa_can_not_authenticate_with_invalid_password_via_nis(): void
    {
        $siswa = User::create([
            'name' => 'Siswa Penguji',
            'email' => 'siswa@smkn1bangsri.sch.id',
            'password' => Hash::make('password'),
        ]);

        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '4710',
        ]);

        $this->post('/login', [
            'email' => '4710',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_can_not_authenticate_with_unregistered_numeric_identity(): void
    {
        $this->post('/login', [
            'email' => '9999999999',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
