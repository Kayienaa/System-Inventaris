<?php

namespace App\Services;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SiPintuSyncService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sipintu.base_url', config('sipintu.api_url', 'http://sipintu.smkn1bangsri.sch.id')), '/');
        $this->clientId = config('services.sipintu.client_id', config('sipintu.client_id', 'contohclientid_12983'));
        $this->clientSecret = config('services.sipintu.client_secret', config('sipintu.client_secret', 'contohclientsecret_72130134'));
        $this->timeout = (int) config('sipintu.timeout', 60);
    }

    /**
     * HTTP Client with authentication headers.
     */
    protected function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withHeaders([
                'Accept'          => 'application/json',
                'X-Client-ID'     => $this->clientId,
                'X-Client-Secret' => $this->clientSecret,
            ]);
    }

    /**
     * Sinkronisasi data Siswa dari SiPintu Gateway ke database lokal.
     * Endpoint: GET {base_url}/api/v1/sijuna/students
     */
    /**
     * Sinkronisasi data Siswa dari SiPintu Gateway ke database lokal.
     * Endpoint: GET {base_url}/api/v1/sijuna/students
     */
    public function syncStudents(): array
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        try {
            $response = $this->client()->get('/api/v1/sijuna/students');

            if (! $response->successful()) {
                $errorMsg = 'Gagal mengambil data siswa dari SiPintu: HTTP ' . $response->status();
                Log::error($errorMsg . ' - Body: ' . $response->body());

                return [
                    'success' => false,
                    'type'    => 'students',
                    'message' => $errorMsg,
                    'synced'  => 0,
                    'created' => 0,
                    'updated' => 0,
                    'errors'  => 0,
                ];
            }

            $payload = $response->json();
            $students = $payload['data'] ?? (is_array($payload) ? $payload : []);

            $created = 0;
            $updated = 0;
            $errors = 0;
            $loggedStudentSample = false;

            // In-memory cache lookup untuk memproses ribuan data siswa dalam hitungan detik
            $existingProfilesByNis = SiswaProfile::all()->keyBy('nis');
            $existingProfilesByUserId = SiswaProfile::all()->keyBy('user_id');
            $existingUsersByEmail = User::with('roles')->get()->keyBy('email');
            $existingUsersById = User::with('roles')->get()->keyBy('id');
            $defaultPasswordHash = Hash::make('password');

            // Proses per chunk 250 record dengan transaksi database efisien
            $chunks = array_chunk($students, 250);

            foreach ($chunks as $chunk) {
                DB::transaction(function () use (
                    $chunk,
                    &$created,
                    &$updated,
                    &$errors,
                    &$loggedStudentSample,
                    &$existingProfilesByNis,
                    &$existingProfilesByUserId,
                    &$existingUsersByEmail,
                    &$existingUsersById,
                    $defaultPasswordHash
                ) {
                    foreach ($chunk as $studentData) {
                        try {
                            // Log sample data siswa yang memiliki no HP untuk verifikasi JSON gateway
                            if (! $loggedStudentSample && (! empty($studentData['hp']) || ! empty($studentData['phone']) || ! empty($studentData['no_hp']) || ! empty($studentData['nomor_hp']))) {
                                Log::info('SiPintu Student JSON Sample with Phone: ' . json_encode($studentData));
                                $loggedStudentSample = true;
                            }

                            $nis = trim((string) ($studentData['nis'] ?? ''));
                            $email = trim((string) ($studentData['user']['email'] ?? ($studentData['email'] ?? ($nis ? "{$nis}@smkn1bangsri.sch.id" : ''))));
                            $name = trim((string) ($studentData['nama'] ?? ($studentData['user']['name'] ?? ($studentData['name'] ?? 'Siswa'))));
                            $nisn = ! empty($studentData['nisn']) ? trim((string) $studentData['nisn']) : null;
                            $className = ! empty($studentData['class_name'])
                                ? trim((string) $studentData['class_name'])
                                : (! empty($studentData['kelas']) ? trim((string) $studentData['kelas']) : null);
                            $phone = trim((string) (
                                $studentData['hp']
                                ?? $studentData['phone']
                                ?? $studentData['no_hp']
                                ?? $studentData['nomor_hp']
                                ?? $studentData['telepon']
                                ?? $studentData['telp']
                                ?? $studentData['phone_number']
                                ?? ($studentData['user']['phone'] ?? null)
                                ?? ($studentData['user']['no_hp'] ?? null)
                                ?? ($studentData['user']['hp'] ?? null)
                                ?? ''
                            )) ?: null;

                            if ($email === '' && $nis === '') {
                                continue;
                            }

                            // 1. Pencocokan User via cache in-memory
                            $existingProfile = $nis !== '' ? ($existingProfilesByNis[$nis] ?? null) : null;
                            $user = ($existingProfile && isset($existingUsersById[$existingProfile->user_id]))
                                ? $existingUsersById[$existingProfile->user_id]
                                : ($email !== '' ? ($existingUsersByEmail[$email] ?? null) : null);

                            if ($user) {
                                $needsUpdate = ($user->name !== $name) || ($email !== '' && $user->email !== $email);
                                if ($needsUpdate) {
                                    $user->name = $name;
                                    if ($email !== '') {
                                        $user->email = $email;
                                    }
                                    $user->save();
                                }
                                $updated++;
                            } else {
                                $user = User::create([
                                    'name'              => $name,
                                    'email'             => $email,
                                    'password'          => $defaultPasswordHash,
                                    'email_verified_at' => now(),
                                ]);
                                if ($email !== '') {
                                    $existingUsersByEmail[$email] = $user;
                                }
                                $existingUsersById[$user->id] = $user;
                                $created++;
                            }

                            // 2. Simpan / update SiswaProfile
                            if ($nis !== '') {
                                $siswaProfile = $existingProfilesByUserId[$user->id] 
                                    ?? ($existingProfilesByNis[$nis] ?? new SiswaProfile(['user_id' => $user->id]));

                                $siswaProfile->user_id = $user->id;
                                $siswaProfile->nis = $nis;
                                $siswaProfile->nisn = $nisn;
                                $siswaProfile->class_name = $className;

                                if (! empty($phone)) {
                                    $siswaProfile->phone = $phone;
                                }

                                $siswaProfile->save();

                                $existingProfilesByNis[$nis] = $siswaProfile;
                                $existingProfilesByUserId[$user->id] = $siswaProfile;

                                // 3. Tetapkan role Spatie 'siswa' HANYA setelah profile berhasil disimpan
                                if (! $user->relationLoaded('roles') || ! $user->roles->contains('name', 'siswa')) {
                                    if (! $user->hasRole('siswa')) {
                                        $user->assignRole('siswa');
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning("Error syncing student record (" . ($studentData['nis'] ?? 'unknown') . "): " . $e->getMessage());
                            $errors++;
                        }
                    }
                });
            }

            return [
                'success' => true,
                'type'    => 'students',
                'message' => 'Sinkronisasi data siswa selesai.',
                'synced'  => $created + $updated,
                'created' => $created,
                'updated' => $updated,
                'errors'  => $errors,
            ];
        } catch (\Throwable $e) {
            Log::error('SiPintu syncStudents exception: ' . $e->getMessage());

            return [
                'success' => false,
                'type'    => 'students',
                'message' => 'Exception saat sinkronisasi siswa: ' . $e->getMessage(),
                'synced'  => 0,
                'created' => 0,
                'updated' => 0,
                'errors'  => 1,
            ];
        }
    }

    /**
     * Sinkronisasi data Guru dari SiPintu Gateway ke database lokal.
     * Endpoint: GET {base_url}/api/v1/sijuna/teachers
     */
    public function syncTeachers(): array
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        try {
            $response = $this->client()->get('/api/v1/sijuna/teachers');

            if (! $response->successful()) {
                $errorMsg = 'Gagal mengambil data guru dari SiPintu: HTTP ' . $response->status();
                Log::error($errorMsg . ' - Body: ' . $response->body());

                return [
                    'success' => false,
                    'type'    => 'teachers',
                    'message' => $errorMsg,
                    'synced'  => 0,
                    'created' => 0,
                    'updated' => 0,
                    'errors'  => 0,
                ];
            }

            $payload = json_decode($response->body(), true, 512, JSON_BIGINT_AS_STRING) ?? $response->json();
            $teachers = $payload['data'] ?? (is_array($payload) ? $payload : []);

            $created = 0;
            $updated = 0;
            $errors = 0;
            $loggedTeacherSample = false;

            // In-memory cache lookup untuk guru
            $existingProfilesByNip = GuruProfile::all()->keyBy('nip');
            $existingProfilesByUserId = GuruProfile::all()->keyBy('user_id');
            $existingUsersByEmail = User::with('roles')->get()->keyBy('email');
            $existingUsersById = User::with('roles')->get()->keyBy('id');
            $defaultPasswordHash = Hash::make('password');

            $chunks = array_chunk($teachers, 250);

            foreach ($chunks as $chunk) {
                DB::transaction(function () use (
                    $chunk,
                    &$created,
                    &$updated,
                    &$errors,
                    &$loggedTeacherSample,
                    &$existingProfilesByNip,
                    &$existingProfilesByUserId,
                    &$existingUsersByEmail,
                    &$existingUsersById,
                    $defaultPasswordHash
                ) {
                    foreach ($chunk as $teacherData) {
                        try {
                            if (! $loggedTeacherSample && (! empty($teacherData['hp']) || ! empty($teacherData['phone']) || ! empty($teacherData['no_hp']) || ! empty($teacherData['nomor_hp']))) {
                                Log::info('SiPintu Teacher JSON Sample with Phone: ' . json_encode($teacherData));
                                $loggedTeacherSample = true;
                            }

                            $rawNip = $teacherData['nip'] ?? '';
                            if (is_float($rawNip) || (is_numeric($rawNip) && str_contains((string) $rawNip, 'E'))) {
                                $nip = number_format((float) $rawNip, 0, '', '');
                            } else {
                                $nip = trim((string) $rawNip);
                            }

                            $email = trim((string) ($teacherData['user']['email'] ?? ($teacherData['email'] ?? ($nip ? "{$nip}@smkn1bangsri.sch.id" : ''))));
                            $name = trim((string) ($teacherData['nama'] ?? ($teacherData['name'] ?? ($teacherData['user']['name'] ?? 'Guru'))));
                            $phone = trim((string) (
                                $teacherData['hp']
                                ?? $teacherData['phone']
                                ?? $teacherData['no_hp']
                                ?? $teacherData['telepon']
                                ?? ($teacherData['user']['phone'] ?? null)
                                ?? ($teacherData['user']['hp'] ?? null)
                                ?? ''
                            )) ?: null;
                            $code = trim((string) ($teacherData['kode'] ?? ($teacherData['code'] ?? '')));

                            if ($email === '' && $nip === '') {
                                continue;
                            }

                            // 1. Pencocokan User via cache in-memory
                            $existingProfile = $nip !== '' ? ($existingProfilesByNip[$nip] ?? null) : null;
                            $user = ($existingProfile && isset($existingUsersById[$existingProfile->user_id]))
                                ? $existingUsersById[$existingProfile->user_id]
                                : ($email !== '' ? ($existingUsersByEmail[$email] ?? null) : null);

                            if ($user) {
                                if (! empty($name)) {
                                    $user->name = $name;
                                }
                                if ($email !== '') {
                                    $user->email = $email;
                                }
                                $user->save();
                                $updated++;
                            } else {
                                $user = User::create([
                                    'name'              => ! empty($name) ? $name : ($email ?: 'Guru'),
                                    'email'             => $email,
                                    'password'          => $defaultPasswordHash,
                                    'email_verified_at' => now(),
                                ]);
                                if ($email !== '') {
                                    $existingUsersByEmail[$email] = $user;
                                }
                                $existingUsersById[$user->id] = $user;
                                $created++;
                            }

                            // 2. Simpan / update GuruProfile (selalu menimpa phone jika disediakan dari SiPintu)
                            if ($nip !== '') {
                                $existingProfile = $existingProfilesByUserId[$user->id] 
                                    ?? ($existingProfilesByNip[$nip] ?? null);

                                $guruProfile = GuruProfile::updateOrCreate(
                                    ['user_id' => $user->id],
                                    [
                                        'nip'   => (string) $nip,
                                        'code'  => $code ?: ($existingProfile?->code ?? null),
                                        'phone' => $phone ?? $existingProfile?->phone,
                                    ]
                                );

                                $existingProfilesByNip[$nip] = $guruProfile;
                                $existingProfilesByUserId[$user->id] = $guruProfile;

                                // 3. Tetapkan role Spatie 'guru' HANYA setelah profile berhasil disimpan
                                if (! $user->relationLoaded('roles') || ! $user->roles->contains('name', 'guru')) {
                                    if (! $user->hasRole('guru')) {
                                        $user->assignRole('guru');
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning("Error syncing teacher record (" . ($teacherData['nip'] ?? 'unknown') . "): " . $e->getMessage());
                            $errors++;
                        }
                    }
                });
            }

            return [
                'success' => true,
                'type'    => 'teachers',
                'message' => 'Sinkronisasi data guru selesai.',
                'synced'  => $created + $updated,
                'created' => $created,
                'updated' => $updated,
                'errors'  => $errors,
            ];
        } catch (\Throwable $e) {
            Log::error('SiPintu syncTeachers exception: ' . $e->getMessage());

            return [
                'success' => false,
                'type'    => 'teachers',
                'message' => 'Exception saat sinkronisasi guru: ' . $e->getMessage(),
                'synced'  => 0,
                'created' => 0,
                'updated' => 0,
                'errors'  => 1,
            ];
        }
    }

    /**
     * Sinkronisasi seluruh data (Siswa & Guru).
     */
    public function syncAll(): array
    {
        $students = $this->syncStudents();
        $teachers = $this->syncTeachers();

        return [
            'success'  => $students['success'] && $teachers['success'],
            'students' => $students,
            'teachers' => $teachers,
        ];
    }
}
