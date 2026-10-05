<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PakAgungSuperAdminSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(UserSeeder::class);
    }

    public function test_pak_agung_can_see_super_admin_shortcut_button(): void
    {
        $pakAgung = User::factory()->create([
            'name' => 'Dwi Agung Suhartono, S.T',
            'email' => 'agungmikro2@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $pakAgung->assignRole('guru');
        GuruProfile::create([
            'user_id' => $pakAgung->id,
            'nip' => '198103302010011016',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($pakAgung)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('⚡');
        $response->assertSee('Masuk Super Admin');
        $response->assertSee(route('switch-to-super-admin'));
    }

    public function test_regular_user_cannot_see_super_admin_shortcut_button(): void
    {
        $siswa = User::factory()->create([
            'name' => 'Ahmad Siswa',
            'email' => 'siswa@smkn1bangsri.sch.id',
        ]);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567891',
        ]);

        $response = $this->actingAs($siswa)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Masuk Super Admin');
    }

    public function test_non_pak_agung_cannot_call_switch_to_super_admin_endpoint(): void
    {
        $guruLain = User::factory()->create([
            'name' => 'Guru Lain, S.Pd',
            'email' => 'gurulain@gmail.com',
        ]);
        $guruLain->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guruLain->id,
            'nip' => '199001012020011001',
            'phone' => '6281234567892',
        ]);

        $response = $this->actingAs($guruLain)->post(route('switch-to-super-admin'), [
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    public function test_switch_to_super_admin_fails_with_wrong_password(): void
    {
        $pakAgung = User::factory()->create([
            'name' => 'Dwi Agung Suhartono, S.T',
            'email' => 'agungmikro2@gmail.com',
        ]);
        $pakAgung->assignRole('guru');
        GuruProfile::create([
            'user_id' => $pakAgung->id,
            'nip' => '198103302010011016',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($pakAgung)->from(route('dashboard'))->post(route('switch-to-super-admin'), [
            'password' => 'salah-password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Kata sandi Super Admin salah');
        $this->assertAuthenticatedAs($pakAgung);
    }

    public function test_switch_to_super_admin_succeeds_with_correct_password(): void
    {
        $pakAgung = User::factory()->create([
            'name' => 'Dwi Agung Suhartono, S.T',
            'email' => 'agungmikro2@gmail.com',
        ]);
        $pakAgung->assignRole('guru');
        GuruProfile::create([
            'user_id' => $pakAgung->id,
            'nip' => '198103302010011016',
            'phone' => '6281234567890',
        ]);

        $superAdmin = User::where('email', 'AdminInventaris@gmail.com')->firstOrFail();

        $response = $this->actingAs($pakAgung)->post(route('switch-to-super-admin'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.borrowings.index'));
        $response->assertSessionHas('success');
        $this->assertAuthenticatedAs($superAdmin);
    }
}
