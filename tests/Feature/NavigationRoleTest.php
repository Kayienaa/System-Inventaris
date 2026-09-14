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

    public function test_super_admin_can_see_full_administration_and_gateway_menu(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kategori');
        $response->assertSee(route('categories.index'));
        $response->assertSee('Administrasi');
        $response->assertSee('Kelola Aset');
        $response->assertSee('Audit Log');
        $response->assertSee('Data Pengguna');
        $response->assertSee('Data Guru');
        $response->assertSee('Gateway SiPintu');
        $response->assertSee(route('admin.assets.index'));
        $response->assertDontSee(route('assets.index'));
    }

    public function test_admin_can_see_operational_admin_menu_without_gateway_and_audit(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kategori');
        $response->assertSee(route('categories.index'));
        $response->assertSee('Administrasi');
        $response->assertSee('Kelola Aset');
        $response->assertSee('Monitoring Peminjaman');
        $response->assertSee(route('admin.assets.index'));
        $response->assertDontSee(route('assets.index'));

        // Menu eksklusif super_admin tidak boleh terlihat oleh admin operasional
        $response->assertDontSee('Audit Log');
        $response->assertDontSee('Gateway SiPintu');
    }

    public function test_siswa_and_guru_only_see_main_user_menu(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        \App\Models\SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($siswa)->get(route('dashboard'));

        $response->assertStatus(200);
        // Menu utama peminjam yang wajib terlihat
        $response->assertSee('Dashboard');
        $response->assertSee('Barang');
        $response->assertSee(route('assets.index'));
        $response->assertSee('Peminjaman');
        $response->assertSee(route('borrowings.mine'));

        // Menu kategori dan administrasi tidak boleh terlihat oleh siswa
        $response->assertDontSee(route('admin.assets.index'));
        $response->assertDontSee(route('categories.index'));
        $response->assertDontSee('Kelola Aset');
        $response->assertDontSee('Audit Log');
        $response->assertDontSee('Gateway SiPintu');

        // Verifikasi juga untuk role guru
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        \App\Models\GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198501012010011099',
            'phone' => '6281234567899',
        ]);

        $guruResponse = $this->actingAs($guru)->get(route('dashboard'));
        $guruResponse->assertStatus(200);
        $guruResponse->assertSee('Dashboard');
        $guruResponse->assertSee('Barang');
        $guruResponse->assertSee(route('assets.index'));
        $guruResponse->assertSee('Peminjaman');
        $guruResponse->assertDontSee(route('categories.index'));
        $guruResponse->assertDontSee('Kelola Aset');
    }

    public function test_non_admin_cannot_access_administrative_routes(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        \App\Models\GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198501012010011088',
            'phone' => '6281234567891',
        ]);

        // Access to audit logs blocked
        $this->actingAs($guru)->get(route('admin.audit-logs.index'))->assertStatus(403);

        // Access to analytics reports blocked
        $this->actingAs($guru)->get(route('dashboard.analytics'))->assertStatus(403);

        // Access to SiPintu admin endpoints blocked
        $this->actingAs($guru)->get(route('sipintu.index'))->assertStatus(403);
    }
}
