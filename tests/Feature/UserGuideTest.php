<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGuideTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login_when_accessing_user_guide(): void
    {
        $response = $this->get(route('guides.user'));
        $response->assertRedirect(route('login'));
    }

    public function test_siswa_can_access_guide_page_and_see_all_9_steps_and_images(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '778899',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($siswa)->get(route('guides.user'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Alur Peminjaman Alat &amp; Barang', false);
        $response->assertSee('Mode Komputer / Desktop');
        $response->assertSee('Mode Ponsel / HP');

        // Verifikasi keberadaan teks dan file 9 Langkah SOP
        $steps = [
            ['title' => 'Akses Dashboard &amp; Tombol Mulai Pinjam', 'desktop' => 'step-1-dashboard-cta.png', 'mobile' => 'step-1-dashboard-cta-mobile.png'],
            ['title' => 'Pilih Unit di Katalog Barang', 'desktop' => 'step-2-katalog-tersedia.png', 'mobile' => 'step-2-katalog-tersedia-mobile.png'],
            ['title' => 'Pengisian Formulir &amp; Tenggat Waktu', 'desktop' => 'step-3-form-pengajuan.png', 'mobile' => 'step-3-form-pengajuan-mobile.png'],
            ['title' => 'Menunggu Serah Terima (Timer 10 Menit Mas Donny)', 'desktop' => 'step-4-countdown-reservasi.png', 'mobile' => 'step-4-countdown-reservasi-mobile.png'],
            ['title' => 'Persetujuan Admin &amp; Tombol Ambil Barang', 'desktop' => 'step-5-status-approved.png', 'mobile' => 'step-5-status-approved-mobile.png'],
            ['title' => 'Pemotretan Kamera Serah Terima di Lab', 'desktop' => 'step-6-kamera-serah-terima.png', 'mobile' => 'step-6-kamera-serah-terima-mobile.png'],
            ['title' => 'Penggunaan Unit &amp; Menu Peminjaman Saya', 'desktop' => 'step-7-sedang-dipinjam.png', 'mobile' => 'step-7-sedang-dipinjam-mobile.png'],
            ['title' => 'Pemotretan Kamera Pengembalian Unit', 'desktop' => 'step-8-kamera-pengembalian.png', 'mobile' => 'step-8-kamera-pengembalian-mobile.png'],
            ['title' => 'Verifikasi Akhir oleh Admin', 'desktop' => 'step-9-menunggu-verifikasi.png', 'mobile' => 'step-9-menunggu-verifikasi-mobile.png'],
        ];

        foreach ($steps as $step) {
            $response->assertSee($step['title'], false);
            $response->assertSee($step['desktop'], false);
            $response->assertSee($step['mobile'], false);
        }
    }

    public function test_guru_can_access_guide_page(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198901012015011001',
            'phone' => '6289876543210',
        ]);

        $response = $this->actingAs($guru)->get(route('guides.user'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Alur Peminjaman Alat &amp; Barang', false);
    }

    public function test_navigation_sidebar_includes_guide_link_for_peminjam_and_bottom_bar_is_removed(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '556677',
            'phone' => '6281112223334',
        ]);

        $response = $this->actingAs($siswa)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('guides.user'));
        $response->assertSee('Panduan');

        // Pastikan komponen mobile bottom navigation bar sudah dihilangkan
        $response->assertDontSee('Navigasi Bawah Peminjam');
    }

    public function test_admin_cannot_access_user_guide_returns_403(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('guides.user'));

        $response->assertStatus(403);
    }

    public function test_super_admin_cannot_access_user_guide_returns_403(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('guides.user'));

        $response->assertStatus(403);
    }
}
