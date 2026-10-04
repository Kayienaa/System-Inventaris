<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGuideTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_guides(): void
    {
        $response = $this->get(route('admin.guides.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_guides_and_see_all_8_steps_and_images(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.guides.index'));

        $response->assertStatus(200);
        $response->assertSee('Panduan SOP Operasional Administrator &amp; Teknisi Lab', false);
        $response->assertSee('Mode Komputer / Desktop');
        $response->assertSee('Mode Ponsel / HP');

        // Verifikasi keberadaan teks judul dan berkas 8 Langkah SOP Admin
        $steps = [
            [
                'title' => 'Monitoring Dasbor &amp; Metrik Transaksi Lab',
                'desktop' => 'admin-step-1-monitoring.png',
                'mobile' => 'admin-step-1-monitoring-mobile.png',
            ],
            [
                'title' => 'Review &amp; Persetujuan Permohonan (Approval / Reject)',
                'desktop' => 'admin-step-2-approval.png',
                'mobile' => 'admin-step-2-approval-mobile.png',
            ],
            [
                'title' => 'Pengawasan Transaksi Aktif &amp; Pintasan WhatsApp',
                'desktop' => 'admin-step-3-whatsapp-shortcut.png',
                'mobile' => 'admin-step-3-whatsapp-shortcut-mobile.png',
            ],
            [
                'title' => 'Pendampingan Kamera Serah Terima Real-Time di Lab',
                'desktop' => 'admin-step-4-bukti-serah-terima.png',
                'mobile' => 'admin-step-4-bukti-serah-terima-mobile.png',
            ],
            [
                'title' => 'Inspeksi Fisik &amp; Verifikasi Pengembalian Unit',
                'desktop' => 'admin-step-5-verifikasi-kembali.png',
                'mobile' => 'admin-step-5-verifikasi-kembali-mobile.png',
            ],
            [
                'title' => 'Pengelolaan Master Inventaris 31 Unit Aset',
                'desktop' => 'admin-step-6-master-aset.png',
                'mobile' => 'admin-step-6-master-aset-mobile.png',
            ],
            [
                'title' => 'Sinkronisasi Terpadu Gateway SiPintu (Super Admin)',
                'desktop' => 'admin-step-7-sipintu-gateway.png',
                'mobile' => 'admin-step-7-sipintu-gateway-mobile.png',
            ],
            [
                'title' => 'Forensik Audit Log &amp; Ekspor Rekapitulasi Laporan',
                'desktop' => 'admin-step-8-audit-log.png',
                'mobile' => 'admin-step-8-audit-log-mobile.png',
            ],
        ];

        foreach ($steps as $step) {
            $response->assertSee($step['title'], false);
            $response->assertSee($step['desktop'], false);
            $response->assertSee($step['mobile'], false);
        }
    }

    public function test_super_admin_can_access_admin_guides(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.guides.index'));

        $response->assertStatus(200);
        $response->assertSee('Panduan SOP Operasional Administrator &amp; Teknisi Lab', false);
    }

    public function test_siswa_cannot_access_admin_guides_returns_403(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '112233',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($siswa)->get(route('admin.guides.index'));

        $response->assertStatus(403);
    }

    public function test_guru_cannot_access_admin_guides_returns_403(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '199001012015011002',
            'phone' => '6289876543211',
        ]);

        $response = $this->actingAs($guru)->get(route('admin.guides.index'));

        $response->assertStatus(403);
    }

    public function test_navigation_sidebar_includes_admin_guide_link_for_admin_and_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.guides.index'));
        $response->assertSee('Panduan SOP Admin');

        // Siswa tidak boleh melihat tautan Panduan SOP Admin
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '445566',
            'phone' => '6281234567891',
        ]);

        $siswaResponse = $this->actingAs($siswa)->get(route('dashboard'));
        $siswaResponse->assertStatus(200);
        $siswaResponse->assertDontSee(route('admin.guides.index'));
        $siswaResponse->assertDontSee('Panduan SOP Admin');
    }
}
