<?php

namespace Tests\Feature;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use App\Services\SiPintuSyncService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SiPintuSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_sync_students_creates_and_updates_users_with_siswa_profile(): void
    {
        $mockUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/') . '/api/v1/sijuna/students';

        Http::fake([
            $mockUrl => Http::response([
                'success' => true,
                'count' => 2,
                'data' => [
                    [
                        'id' => 101,
                        'nis' => '212210001',
                        'nisn' => '0051234567',
                        'nama' => 'Ahmad Dani',
                        'hp' => '081234567890',
                        'kelas' => 'XII RPL 1',
                        'user' => [
                            'email' => 'ahmad.dani@smkn1bangsri.sch.id',
                            'name' => 'Ahmad Dani',
                        ],
                    ],
                    [
                        'id' => 102,
                        'nis' => '212210002',
                        'nisn' => '0051234568',
                        'nama' => 'Siti Nurhaliza',
                        'hp' => '081234567891',
                        'kelas' => 'XII RPL 2',
                        'user' => [
                            'email' => 'siti.nur@smkn1bangsri.sch.id',
                            'name' => 'Siti Nurhaliza',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(SiPintuSyncService::class);
        $result = $service->syncStudents();

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['created']);

        // Verifikasi User dibuat dan memiliki role siswa
        $studentUser = User::where('email', 'ahmad.dani@smkn1bangsri.sch.id')->first();
        $this->assertNotNull($studentUser);
        $this->assertTrue($studentUser->hasRole('siswa'));

        // Verifikasi SiswaProfile tersimpan
        $profile = SiswaProfile::where('user_id', $studentUser->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('212210001', $profile->nis);
        $this->assertEquals('0051234567', $profile->nisn);
        $this->assertEquals('XII RPL 1', $profile->class_name);
    }

    public function test_sync_teachers_creates_and_updates_users_with_guru_profile(): void
    {
        $mockUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/') . '/api/v1/sijuna/teachers';

        Http::fake([
            $mockUrl => Http::response([
                'success' => true,
                'count' => 1,
                'data' => [
                    [
                        'id' => 201,
                        'nip' => '198001012005011001',
                        'nama' => 'Budi Santoso, S.Kom.',
                        'hp' => '081987654321',
                        'user' => [
                            'email' => 'budi.santoso@smkn1bangsri.sch.id',
                            'name' => 'Budi Santoso, S.Kom.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(SiPintuSyncService::class);
        $result = $service->syncTeachers();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['created']);

        // Verifikasi User dibuat dan memiliki role guru
        $teacherUser = User::where('email', 'budi.santoso@smkn1bangsri.sch.id')->first();
        $this->assertNotNull($teacherUser);
        $this->assertTrue($teacherUser->hasRole('guru'));

        // Verifikasi GuruProfile tersimpan
        $profile = GuruProfile::where('user_id', $teacherUser->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('198001012005011001', $profile->nip);
        $this->assertEquals('081987654321', $profile->phone);
    }

    public function test_artisan_command_syncs_users_successfully(): void
    {
        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'nis' => '11111',
                        'nama' => 'Student Test',
                        'user' => ['email' => 'student@test.com'],
                    ],
                ],
            ], 200),
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 2,
                        'nip' => '22222',
                        'nama' => 'Teacher Test',
                        'user' => ['email' => 'teacher@test.com'],
                    ],
                ],
            ], 200),
        ]);

        $this->artisan('sipintu:sync-users --type=all')
            ->expectsOutputToContain('Memulai proses sinkronisasi')
            ->expectsOutputToContain('Siswa')
            ->expectsOutputToContain('Guru')
            ->assertSuccessful();
    }

    public function test_guru_and_siswa_profile_is_locked_to_view_only(): void
    {
        $siswa = User::factory()->create([
            'name' => 'Siswa Original',
            'email' => 'siswa@smkn1bangsri.sch.id',
        ]);
        $siswa->assignRole('siswa');

        // Siswa melihat halaman profil
        $response = $this->actingAs($siswa)->get('/profile');
        $response->assertOk();
        $response->assertSee('Profil Dikelola Terpusat via SiPintu');
        $response->assertSee('Mode Lihat Saja (Read-Only)');

        // Siswa mencoba mengubah profil via PATCH
        $patchResponse = $this->actingAs($siswa)->patch('/profile', [
            'name' => 'Hacked Name',
            'email' => 'hacked@smkn1bangsri.sch.id',
        ]);

        $patchResponse->assertRedirect('/profile');
        $patchResponse->assertSessionHas('error');

        // Pastikan data tidak berubah di database
        $siswa->refresh();
        $this->assertEquals('Siswa Original', $siswa->name);
        $this->assertEquals('siswa@smkn1bangsri.sch.id', $siswa->email);
    }

    public function test_admin_can_trigger_sync_all_via_web_interface(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 10,
                        'nis' => '99001',
                        'nama' => 'Student Sync All',
                        'user' => ['email' => 'student.sync@test.com'],
                    ],
                ],
            ], 200),
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 20,
                        'nip' => '199001012020011001',
                        'nama' => 'Teacher Sync All',
                        'user' => ['email' => 'teacher.sync@test.com'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'all',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Sinkronisasi SiPintu berhasil!', session('success'));
    }

    public function test_admin_can_trigger_sync_students_via_web_interface(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 11,
                        'nis' => '99002',
                        'nama' => 'Student Sync Only',
                        'user' => ['email' => 'student.only@test.com'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'students',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Sinkronisasi SiPintu berhasil!', session('success'));
    }

    public function test_admin_can_trigger_sync_teachers_via_web_interface(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 21,
                        'nip' => '199001012020011002',
                        'nama' => 'Teacher Sync Only',
                        'user' => ['email' => 'teacher.only@test.com'],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'teachers',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Sinkronisasi SiPintu berhasil!', session('success'));
    }

    public function test_non_admin_cannot_trigger_sync_via_web_interface(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->actingAs($siswa)->post('/admin/sync-sipintu', [
            'type' => 'all',
        ]);

        $response->assertForbidden();
    }

    public function test_sync_handles_connection_failure_gracefully(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response('Gateway Timeout', 504),
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response('Gateway Timeout', 504),
        ]);

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'all',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
