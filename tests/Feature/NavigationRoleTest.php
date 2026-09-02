<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_see_administration_menu(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Administrasi');
        $response->assertSee('Audit Log');
        $response->assertSee('Data Pengguna');
        $response->assertSee('Data Guru');
        $response->assertSee('Gateway SiPintu');
    }

    public function test_siswa_and_guru_only_see_main_user_menu(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->actingAs($siswa)->get(route('dashboard'));

        $response->assertStatus(200);
        // Menu utama yang wajib terlihat
        $response->assertSee('Dashboard');
        $response->assertSee('Barang');
        $response->assertSee('Kategori');
        $response->assertSee('Peminjaman');

        // Menu administrasi tidak boleh ada di navigasi siswa
        $response->assertDontSee('Audit Log');
        $response->assertDontSee('Gateway SiPintu');
    }

    public function test_non_admin_cannot_access_administrative_routes(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        // Access to audit logs blocked
        $this->actingAs($guru)->get(route('admin.audit-logs.index'))->assertStatus(403);

        // Access to analytics reports blocked
        $this->actingAs($guru)->get(route('dashboard.analytics'))->assertStatus(403);

        // Access to SiPintu admin endpoints blocked
        $this->actingAs($guru)->get(route('sipintu.index'))->assertStatus(403);
    }
}
