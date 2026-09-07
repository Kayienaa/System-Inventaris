<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompletePhoneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'siswa']);
    }

    public function test_guest_is_redirected_to_login_from_complete_phone(): void
    {
        $response = $this->get('/complete-phone');
        $response->assertRedirect('/login');
    }

    public function test_admin_is_redirected_to_dashboard_from_complete_phone(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/complete-phone');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_siswa_with_phone_is_redirected_to_dashboard_from_complete_phone(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '1001',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($siswa)->get('/complete-phone');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_siswa_without_phone_is_redirected_to_complete_phone_when_accessing_dashboard(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '1002',
            'phone' => null,
        ]);

        $response = $this->actingAs($siswa)->get(route('dashboard'));
        $response->assertRedirect(route('complete-phone'));
    }

    public function test_guru_without_phone_is_redirected_to_complete_phone_when_accessing_dashboard(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198501012010011099',
            'phone' => null,
        ]);

        $response = $this->actingAs($guru)->get(route('dashboard'));
        $response->assertRedirect(route('complete-phone'));
    }

    public function test_siswa_can_view_complete_phone_screen(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '1003',
            'phone' => null,
        ]);

        $response = $this->actingAs($siswa)->get('/complete-phone');
        $response->assertOk();
        $response->assertSee('Untuk keperluan konfirmasi peminjaman barang TEFA, silakan masukkan nomor WhatsApp aktif Anda terlebih dahulu.');
        $response->assertSee('Simpan Nomor WhatsApp');
    }

    public function test_siswa_can_save_valid_phone_number_and_redirects_to_dashboard(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '1004',
            'phone' => null,
        ]);

        $response = $this->actingAs($siswa)->post('/complete-phone', [
            'phone' => '081234567890',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Nomor WhatsApp berhasil disimpan! Selamat datang di TE-VAULT.');

        $siswa->refresh();
        $this->assertEquals('6281234567890', $siswa->siswaProfile->phone);
    }

    public function test_guru_can_save_valid_phone_number_and_redirects_to_dashboard(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198501012010011088',
            'phone' => null,
        ]);

        $response = $this->actingAs($guru)->post('/complete-phone', [
            'phone' => '+628987654321',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Nomor WhatsApp berhasil disimpan! Selamat datang di TE-VAULT.');

        $guru->refresh();
        $this->assertEquals('628987654321', $guru->guruProfile->phone);
    }

    public function test_phone_validation_rejects_invalid_numbers(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $invalidNumbers = [
            '12345',         // Terlalu pendek
            '0217654321',    // Nomor telepon rumah/kantor
            'abcd123456',    // Mengandung huruf
            '0800123456',    // Bebas pulsa
        ];

        foreach ($invalidNumbers as $invalid) {
            $response = $this->actingAs($siswa)->post('/complete-phone', [
                'phone' => $invalid,
            ]);

            $response->assertSessionHasErrors(['phone']);
        }
    }

    public function test_siswa_can_update_phone_number_from_profile_page(): void
    {
        $siswa = User::factory()->create([
            'name' => 'Siswa Asli',
            'email' => 'siswa.asli@smkn1bangsri.sch.id',
        ]);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '1005',
            'phone' => '628111111111',
        ]);

        $response = $this->actingAs($siswa)->patch('/profile', [
            'phone' => '082233445566',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success', 'Nomor telepon berhasil diperbarui.');

        $siswa->refresh();
        $this->assertEquals('6282233445566', $siswa->siswaProfile->phone);
        $this->assertEquals('Siswa Asli', $siswa->name);
        $this->assertEquals('siswa.asli@smkn1bangsri.sch.id', $siswa->email);
    }

    public function test_siswa_cannot_update_name_or_email_from_profile_page(): void
    {
        $siswa = User::factory()->create([
            'name' => 'Nama Awal',
            'email' => 'awal@smkn1bangsri.sch.id',
        ]);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '1006',
            'phone' => '628111111111',
        ]);

        // Kirim perubahan nama dan email tanpa nomor telepon
        $response = $this->actingAs($siswa)->patch('/profile', [
            'name' => 'Nama Diretas',
            'email' => 'retas@smkn1bangsri.sch.id',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error');

        $siswa->refresh();
        $this->assertEquals('Nama Awal', $siswa->name);
        $this->assertEquals('awal@smkn1bangsri.sch.id', $siswa->email);
    }

    public function test_siswa_without_profile_row_is_redirected_to_complete_phone(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->actingAs($siswa)->get(route('dashboard'));
        $response->assertRedirect(route('complete-phone'));
    }

    public function test_guru_without_profile_row_is_redirected_to_complete_phone(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $response = $this->actingAs($guru)->get(route('dashboard'));
        $response->assertRedirect(route('complete-phone'));
    }

    public function test_siswa_without_profile_row_submitting_phone_returns_sipintu_sync_error(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->actingAs($siswa)->post('/complete-phone', [
            'phone' => '081234567890',
        ]);

        $response->assertSessionHas('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
        $this->assertDatabaseMissing('siswa_profiles', ['user_id' => $siswa->id]);
    }

    public function test_guru_without_profile_row_submitting_phone_returns_sipintu_sync_error(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $response = $this->actingAs($guru)->post('/complete-phone', [
            'phone' => '081234567890',
        ]);

        $response->assertSessionHas('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
        $this->assertDatabaseMissing('guru_profiles', ['user_id' => $guru->id]);
    }

    public function test_siswa_without_profile_row_updating_profile_returns_sipintu_sync_error(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->withoutMiddleware(\App\Http\Middleware\EnsurePhoneIsFilled::class)
            ->actingAs($siswa)
            ->patch('/profile', [
                'phone' => '081234567890',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
        $this->assertDatabaseMissing('siswa_profiles', ['user_id' => $siswa->id]);
    }

    public function test_reset_borrower_phones_command_clears_phones(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole('siswa');
        $siswaProfile = SiswaProfile::create([
            'user_id' => $user1->id,
            'nis' => '1007',
            'phone' => '082100000001',
        ]);

        $user2 = User::factory()->create();
        $user2->assignRole('guru');
        $guruProfile = GuruProfile::create([
            'user_id' => $user2->id,
            'nip' => '198501012010011077',
            'phone' => '082100000002',
        ]);

        $this->artisan('tefa:reset-borrower-phones')
            ->expectsConfirmation('Tindakan ini akan MENGHAPUS (NULL-kan) SEMUA nomor WhatsApp siswa & guru. Lanjutkan?', 'yes')
            ->expectsOutputToContain('Berhasil mereset kolom nomor WhatsApp')
            ->assertSuccessful();

        $siswaProfile->refresh();
        $guruProfile->refresh();

        $this->assertNull($siswaProfile->phone);
        $this->assertNull($guruProfile->phone);
    }

    public function test_reset_borrower_phones_command_with_force_option_skips_confirmation(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole('siswa');
        $siswaProfile = SiswaProfile::create([
            'user_id' => $user1->id,
            'nis' => '1008',
            'phone' => '082100000003',
        ]);

        $this->artisan('tefa:reset-borrower-phones', ['--force' => true])
            ->expectsOutputToContain('Berhasil mereset kolom nomor WhatsApp')
            ->assertSuccessful();

        $siswaProfile->refresh();
        $this->assertNull($siswaProfile->phone);
    }

    public function test_reset_borrower_phones_command_can_be_cancelled(): void
    {
        $user1 = User::factory()->create();
        $user1->assignRole('siswa');
        $siswaProfile = SiswaProfile::create([
            'user_id' => $user1->id,
            'nis' => '1009',
            'phone' => '082100000004',
        ]);

        $this->artisan('tefa:reset-borrower-phones')
            ->expectsConfirmation('Tindakan ini akan MENGHAPUS (NULL-kan) SEMUA nomor WhatsApp siswa & guru. Lanjutkan?', 'no')
            ->expectsOutputToContain('Operasi reset dibatalkan.')
            ->assertSuccessful();

        $siswaProfile->refresh();
        $this->assertEquals('082100000004', $siswaProfile->phone);
    }
}
