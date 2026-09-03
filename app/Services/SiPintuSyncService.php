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
    public function syncStudents(): array
    {
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

            foreach ($students as $studentData) {
                try {
                    DB::transaction(function () use ($studentData, &$created, &$updated) {
                        $nis = trim((string) ($studentData['nis'] ?? ''));
                        $email = trim((string) ($studentData['user']['email'] ?? ($studentData['email'] ?? ($nis ? "{$nis}@smkn1bangsri.sch.id" : ''))));
                        $name = trim((string) ($studentData['nama'] ?? ($studentData['user']['name'] ?? ($studentData['name'] ?? 'Siswa'))));
                        $nisn = ! empty($studentData['nisn']) ? trim((string) $studentData['nisn']) : null;
                        $className = ! empty($studentData['class_name'])
                            ? trim((string) $studentData['class_name'])
                            : (! empty($studentData['kelas']) ? trim((string) $studentData['kelas']) : null);
                        $phone = ! empty($studentData['hp'])
                            ? trim((string) $studentData['hp'])
                            : (! empty($studentData['phone']) ? trim((string) $studentData['phone']) : null);

                        if ($email === '' && $nis === '') {
                            return;
                        }

                        // Pencocokan data user: via SiswaProfile (NIS) atau Email
                        $existingProfile = $nis !== '' ? SiswaProfile::where('nis', $nis)->first() : null;
                        $user = $existingProfile?->user ?? ($email !== '' ? User::where('email', $email)->first() : null);

                        if ($user) {
                            $user->update([
                                'name'  => $name,
                                'email' => $email !== '' ? $email : $user->email,
                            ]);
                            $updated++;
                        } else {
                            $user = User::create([
                                'name'              => $name,
                                'email'             => $email,
                                'password'          => Hash::make('password'),
                                'email_verified_at' => now(),
                            ]);
                            $created++;
                        }

                        // Tetapkan role Spatie 'siswa'
                        if (! $user->hasRole('siswa')) {
                            $user->assignRole('siswa');
                        }

                        // Simpan / update SiswaProfile
                        if ($nis !== '') {
                            SiswaProfile::updateOrCreate(
                                ['user_id' => $user->id],
                                [
                                    'nis'        => $nis,
                                    'nisn'       => $nisn,
                                    'class_name' => $className,
                                    'phone'      => $phone,
                                ]
                            );
                        }
                    });
                } catch (\Throwable $e) {
                    Log::warning("Error syncing student record (" . ($studentData['nis'] ?? 'unknown') . "): " . $e->getMessage());
                    $errors++;
                }
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

            $payload = $response->json();
            $teachers = $payload['data'] ?? (is_array($payload) ? $payload : []);

            $created = 0;
            $updated = 0;
            $errors = 0;

            foreach ($teachers as $teacherData) {
                try {
                    DB::transaction(function () use ($teacherData, &$created, &$updated) {
                        $nip = trim((string) ($teacherData['nip'] ?? ''));
                        $email = trim((string) ($teacherData['user']['email'] ?? ($teacherData['email'] ?? ($nip ? "{$nip}@smkn1bangsri.sch.id" : ''))));
                        $name = trim((string) ($teacherData['nama'] ?? ($teacherData['user']['name'] ?? ($teacherData['name'] ?? 'Guru'))));
                        $phone = ! empty($teacherData['hp'])
                            ? trim((string) $teacherData['hp'])
                            : (! empty($teacherData['phone']) ? trim((string) $teacherData['phone']) : null);

                        if ($email === '' && $nip === '') {
                            return;
                        }

                        // Pencocokan data user: via GuruProfile (NIP) atau Email
                        $existingProfile = $nip !== '' ? GuruProfile::where('nip', $nip)->first() : null;
                        $user = $existingProfile?->user ?? ($email !== '' ? User::where('email', $email)->first() : null);

                        if ($user) {
                            $user->update([
                                'name'  => $name,
                                'email' => $email !== '' ? $email : $user->email,
                            ]);
                            $updated++;
                        } else {
                            $user = User::create([
                                'name'              => $name,
                                'email'             => $email,
                                'password'          => Hash::make('password'),
                                'email_verified_at' => now(),
                            ]);
                            $created++;
                        }

                        // Tetapkan role Spatie 'guru'
                        if (! $user->hasRole('guru')) {
                            $user->assignRole('guru');
                        }

                        // Simpan / update GuruProfile
                        if ($nip !== '') {
                            GuruProfile::updateOrCreate(
                                ['user_id' => $user->id],
                                [
                                    'nip'   => $nip,
                                    'phone' => $phone,
                                ]
                            );
                        }
                    });
                } catch (\Throwable $e) {
                    Log::warning("Error syncing teacher record (" . ($teacherData['nip'] ?? 'unknown') . "): " . $e->getMessage());
                    $errors++;
                }
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
