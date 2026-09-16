<?php

namespace Tests\Feature;

use App\Jobs\SyncSiPintuJob;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use App\Services\SiPintuSyncService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
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
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);

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
        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response(['success' => true, 'data' => []], 200),
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response(['success' => true, 'data' => []], 200),
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'all',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sinkronisasi telah dijadwalkan dan sedang berjalan di background. Data akan diperbarui dalam beberapa saat — pastikan queue worker aktif (php artisan queue:work).');
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'sipintu.sync_requested',
        ]);
    }

    public function test_admin_can_trigger_sync_students_via_web_interface(): void
    {
        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response(['success' => true, 'data' => []], 200),
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'students',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sinkronisasi telah dijadwalkan dan sedang berjalan di background. Data akan diperbarui dalam beberapa saat — pastikan queue worker aktif (php artisan queue:work).');
    }

    public function test_admin_can_trigger_sync_teachers_via_web_interface(): void
    {
        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response(['success' => true, 'data' => []], 200),
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->post('/admin/sync-sipintu', [
            'type' => 'teachers',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sinkronisasi telah dijadwalkan dan sedang berjalan di background. Data akan diperbarui dalam beberapa saat — pastikan queue worker aktif (php artisan queue:work).');
    }

    public function test_non_admin_cannot_trigger_sync_via_web_interface(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');
        SiswaProfile::create([
            'user_id' => $siswa->id,
            'nis' => '12345',
            'phone' => '6281234567890',
        ]);

        $response = $this->actingAs($siswa)->post('/admin/sync-sipintu', [
            'type' => 'all',
        ]);

        $response->assertForbidden();
    }

    public function test_sync_job_handles_execution_and_records_audit(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

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

        $job = new SyncSiPintuJob('all');
        $job->handle(app(\App\Services\SiPintuSyncService::class), app(\App\Services\AuditLogService::class));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'sipintu.synced',
        ]);
    }

    public function test_sync_job_handles_connection_failure_gracefully(): void
    {
        Log::spy();

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        Http::fake([
            $baseUrl . '/api/v1/sijuna/students' => Http::response('Gateway Timeout', 504),
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response('Gateway Timeout', 504),
        ]);

        $job = new SyncSiPintuJob('all');
        $job->handle(app(\App\Services\SiPintuSyncService::class), app(\App\Services\AuditLogService::class));

        Log::shouldHaveReceived('error')->atLeast()->once();
    }

    public function test_teachers_page_displays_nip_as_exact_string_without_rounding(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $user = User::factory()->create(['name' => 'Iwan Safrudin', 'email' => 'iwansafr@gmail.com']);
        $user->assignRole('guru');
        GuruProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => '199301162022211008',
                'code' => 'AW',
                'phone' => '085758700025',
            ]
        );

        $baseUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/');

        // Simulate Gateway returning numeric/unquoted 18-digit NIP
        Http::fake([
            $baseUrl . '/api/v1/sijuna/teachers' => Http::response('{"success":true,"count":1,"data":[{"id":1,"nip":199301162022211008,"kode":"AW","nama":"Iwan Safrudin","user":{"email":"iwansafr@gmail.com","name":"Iwan Safrudin"}}]}', 200),
            $baseUrl . '/api/v1/ping' => Http::response(['success' => true, 'data' => ['connected' => true]], 200),
        ]);

        app(\App\Services\SiPintuService::class)->clearCache();

        $response = $this->actingAs($admin)->get('/sipintu/guru?search=Iwan');
        $response->assertOk();
        $response->assertSee('199301162022211008');
        $response->assertDontSee('199301162022211000');

        // Also assert AJAX JSON endpoint
        $ajaxResponse = $this->actingAs($admin)->getJson('/sipintu/api/teachers?search=Iwan');
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonFragment([
            'nip' => '199301162022211008',
        ]);
        $this->assertStringContainsString('"nip":"199301162022211008"', $ajaxResponse->getContent());
        $this->assertStringNotContainsString('199301162022211000', $ajaxResponse->getContent());
    }

    public function test_sync_students_skips_graduated_students(): void
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
                        'nama' => 'Active Student',
                        'graduated' => false,
                        'user' => [
                            'email' => 'active@smkn1bangsri.sch.id',
                            'name' => 'Active Student',
                        ],
                    ],
                    [
                        'id' => 102,
                        'nis' => '212210002',
                        'nama' => 'Alumni Student',
                        'graduated' => true,
                        'user' => [
                            'email' => 'alumni@smkn1bangsri.sch.id',
                            'name' => 'Alumni Student',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(SiPintuSyncService::class);
        $result = $service->syncStudents();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['created']);

        $this->assertDatabaseHas('users', ['email' => 'active@smkn1bangsri.sch.id']);
        $this->assertDatabaseMissing('users', ['email' => 'alumni@smkn1bangsri.sch.id']);
        $this->assertDatabaseMissing('siswa_profiles', ['nis' => '212210002']);
    }

    public function test_sipintu_service_raw_students_filters_out_graduates(): void
    {
        $mockUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/') . '/api/v1/sijuna/students';

        Http::fake([
            $mockUrl => Http::response([
                'success' => true,
                'count' => 2,
                'data' => [
                    [
                        'id' => 1,
                        'nis' => '111',
                        'nama' => 'Siswa Aktif',
                        'graduated' => false,
                        'user' => ['email' => 'aktif@test.com'],
                    ],
                    [
                        'id' => 2,
                        'nis' => '222',
                        'nama' => 'Siswa Lulus',
                        'graduate' => true,
                        'user' => ['email' => 'lulus@test.com'],
                    ],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\SiPintuService::class);
        $result = $service->getAllStudentsRaw(true);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['count']);
        $this->assertEquals('111', $result['data'][0]['nis']);
    }

    public function test_purge_alumni_artisan_command_deletes_only_graduates_without_active_borrowings(): void
    {
        $mockUrl = rtrim(config('services.sipintu.base_url', 'http://sipintu.smkn1bangsri.sch.id'), '/') . '/api/v1/sijuna/students';

        // Buat user alumni A yang punya peminjaman aktif
        $alumniA = User::factory()->create(['name' => 'Alumni With Loan', 'email' => 'alumni.loan@smkn1bangsri.sch.id']);
        $alumniA->assignRole('siswa');
        SiswaProfile::create(['user_id' => $alumniA->id, 'nis' => '99001']);

        $category = \App\Models\AssetCategory::create([
            'code' => 'LAB-KOMP',
            'name' => 'Lab Komputer',
        ]);
        $asset = \App\Models\Asset::create([
            'asset_category_id' => $category->id,
            'name' => 'Laptop Dell',
            'asset_code' => 'AST-001',
            'condition' => \App\Enums\AssetCondition::Baik,
            'availability_status' => \App\Enums\AssetAvailabilityStatus::Dipinjam,
        ]);

        \App\Models\Borrowing::create([
            'borrower_user_id' => $alumniA->id,
            'asset_id' => $asset->id,
            'status' => \App\Enums\BorrowingStatus::Borrowed,
            'requested_at' => now()->subDays(2),
            'borrowed_at' => now()->subDays(2),
            'due_at' => now()->addDays(2),
        ]);

        // Buat user alumni B yang TIDAK punya peminjaman
        $alumniB = User::factory()->create(['name' => 'Alumni Clear', 'email' => 'alumni.clear@smkn1bangsri.sch.id']);
        $alumniB->assignRole('siswa');
        SiswaProfile::create(['user_id' => $alumniB->id, 'nis' => '99002']);

        // Mock API SiPintu mengembalikan keduanya sebagai graduated: true
        Http::fake([
            $mockUrl => Http::response([
                'success' => true,
                'data' => [
                    [
                        'id' => 901,
                        'nis' => '99001',
                        'graduated' => true,
                        'user' => ['email' => 'alumni.loan@smkn1bangsri.sch.id'],
                    ],
                    [
                        'id' => 902,
                        'nis' => '99002',
                        'graduate' => true,
                        'user' => ['email' => 'alumni.clear@smkn1bangsri.sch.id'],
                    ],
                ],
            ], 200),
        ]);

        // Jalankan purge command
        $this->artisan('sipintu:purge-alumni')
            ->expectsOutputToContain('Pembersihan akun alumni')
            ->assertSuccessful();

        // Verifikasi Alumni B terhapus
        $this->assertDatabaseMissing('users', ['id' => $alumniB->id]);
        $this->assertDatabaseMissing('siswa_profiles', ['nis' => '99002']);

        // Verifikasi Alumni A tetap ada karena punya pinjaman aktif
        $this->assertDatabaseHas('users', ['id' => $alumniA->id]);
        $this->assertDatabaseHas('siswa_profiles', ['nis' => '99001']);
    }
}


