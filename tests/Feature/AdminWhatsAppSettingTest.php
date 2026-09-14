<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWhatsAppSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_whatsapp_setting_form_on_profile(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        SystemSetting::set('tefa_admin_whatsapp', '6282223005860');

        $response = $this->actingAs($admin)->get('/profile');

        $response->assertOk();
        $response->assertSee('Nomor Pengirim WhatsApp Admin TEFA');
        $response->assertSee('Nomor ini digunakan sebagai identitas resmi kontak pengirim pengingat keterlambatan (overdue) dan pesan notifikasi inventaris TEFA.');
        $response->assertSee('Contoh: 082223005860 atau 6282223005860');
        $response->assertSee('Simpan Nomor Pengirim Admin');
        $response->assertSee('value="6282223005860"', false);
    }

    public function test_super_admin_can_view_whatsapp_setting_form_on_profile(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get('/profile');

        $response->assertOk();
        $response->assertSee('Nomor Pengirim WhatsApp Admin TEFA');
        $response->assertSee('Simpan Nomor Pengirim Admin');
    }

    public function test_admin_can_update_whatsapp_number_and_it_normalizes_to_628(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => '082223005860',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'admin-whatsapp-updated');
        $response->assertSessionHas('success', 'Nomor WhatsApp pengirim admin berhasil diperbarui!');

        $this->assertEquals('6282223005860', SystemSetting::get('tefa_admin_whatsapp'));
        $this->assertEquals('6282223005860', WhatsAppNotificationService::getAdminNumber());
        $this->assertEquals('+62 822-2300-5860', WhatsAppNotificationService::getAdminDisplayPhoneNumber());
    }

    public function test_super_admin_can_update_whatsapp_number(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => '+62 812-9876-5432',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'admin-whatsapp-updated');

        $this->assertEquals('6281298765432', SystemSetting::get('tefa_admin_whatsapp'));
        $this->assertEquals('6281298765432', WhatsAppNotificationService::getAdminNumber());
    }

    public function test_whatsapp_notification_service_prioritizes_database_setting_over_config(): void
    {
        config(['services.whatsapp.admin_number' => '6289999999999']);

        SystemSetting::set('tefa_admin_whatsapp', '6282223005860');

        $this->assertEquals('6282223005860', WhatsAppNotificationService::getAdminNumber());
        $this->assertEquals('6282223005860', WhatsAppNotificationService::getAdminPhoneNumber());
    }

    public function test_whatsapp_notification_service_falls_back_to_config_when_database_is_empty(): void
    {
        config(['services.whatsapp.admin_number' => '081234567890']);

        // Pastikan tidak ada data di database
        SystemSetting::where('key', 'tefa_admin_whatsapp')->delete();

        $this->assertEquals('6281234567890', WhatsAppNotificationService::getAdminNumber());
        $this->assertEquals('6281234567890', WhatsAppNotificationService::getAdminPhoneNumber());
    }

    public function test_siswa_cannot_see_whatsapp_setting_form(): void
    {
        $siswa = User::factory()->create(['name' => 'Siswa Test']);
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '081234567890',
        ]);

        $response = $this->actingAs($siswa)->get('/profile');

        $response->assertOk();
        $response->assertDontSee('Nomor Pengirim WhatsApp Admin TEFA');
        $response->assertDontSee('Simpan Nomor Pengirim Admin');
    }

    public function test_guru_cannot_see_whatsapp_setting_form(): void
    {
        $guru = User::factory()->create(['name' => 'Guru Test']);
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198501012010011002',
            'phone' => '081234567891',
        ]);

        $response = $this->actingAs($guru)->get('/profile');

        $response->assertOk();
        $response->assertDontSee('Nomor Pengirim WhatsApp Admin TEFA');
        $response->assertDontSee('Simpan Nomor Pengirim Admin');
    }

    public function test_siswa_cannot_update_admin_whatsapp_number_returns_403(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '081234567890',
        ]);

        $response = $this->actingAs($siswa)->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => '082223005860',
        ]);

        $response->assertForbidden();
    }

    public function test_guru_cannot_update_admin_whatsapp_number_returns_403(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        GuruProfile::create([
            'user_id' => $guru->id,
            'nip' => '198501012010011002',
            'phone' => '081234567891',
        ]);

        $response = $this->actingAs($guru)->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => '082223005860',
        ]);

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_accessing_update_admin_whatsapp(): void
    {
        $response = $this->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => '082223005860',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_validation_rejects_invalid_whatsapp_number(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Nomor tidak valid (contoh huruf / nomor luar / nomor kabel kantor)
        $response = $this->actingAs($admin)->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => '0211234567',
        ]);

        $response->assertSessionHasErrors('tefa_admin_whatsapp');

        $responseString = $this->actingAs($admin)->patch(route('profile.admin-whatsapp.update'), [
            'tefa_admin_whatsapp' => 'bukan-nomor-telepon',
        ]);

        $responseString->assertSessionHasErrors('tefa_admin_whatsapp');
    }
}
