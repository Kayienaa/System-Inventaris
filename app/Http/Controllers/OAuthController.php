<?php

namespace App\Http\Controllers;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use App\Services\SiPintuSyncService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Menerima otorisasi SSO otomatis dari Portal SiPintu Gateway
     */
    public function callback(Request $request): RedirectResponse
    {
        // 1. Tangkap Authorization Code yang dikirim SiPintu
        $code = $request->input('code');

        if (! $code) {
            return redirect()->route('login')->with('error', 'Otorisasi SSO SiPintu gagal: Kode otorisasi tidak ditemukan.');
        }

        // Replay Protection (Single-Use Code)
        $codeHash = hash('sha256', $code);
        if (! Cache::add("sso_code:{$codeHash}", true, now()->addMinutes(2))) {
            Log::warning('SSO authorization code replay terdeteksi.');

            return redirect()->route('login')->with('error', 'Kode otorisasi sudah digunakan. Silakan login ulang.');
        }

        $baseUrl      = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', config('sipintu.api_url', 'http://localhost:8000'))), '/');
        $clientId     = env('SIPINTU_CLIENT_ID', config('services.sipintu.client_id', config('sipintu.client_id')));
        $clientSecret = env('SIPINTU_CLIENT_SECRET', config('services.sipintu.client_secret', config('sipintu.client_secret')));
        $redirectUri  = env('SIPINTU_REDIRECT_URI', config('services.sipintu.redirect_uri', config('sipintu.redirect_uri', url('/oauth/callback'))));

        // 2. Tukar Code dengan Access Token (Backend-to-Backend HTTP POST)
        try {
            $tokenResponse = Http::asForm()->acceptJson()->post("{$baseUrl}/oauth/token", [
                'grant_type'    => 'authorization_code',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'code'          => $code,
            ]);
        } catch (\Throwable $e) {
            Log::error('SiPintu SSO Token Exchange Exception: ' . $e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal menghubungi server SiPintu Gateway: ' . $e->getMessage());
        }

        if ($tokenResponse->failed()) {
            $errorMsg = $tokenResponse->json('error_description')
                ?? $tokenResponse->json('message')
                ?? 'Gagal memverifikasi token ke SiPintu Gateway.';

            Log::warning('SiPintu SSO Token Exchange Failed: ' . $tokenResponse->body());

            return redirect()->route('login')->with('error', $errorMsg);
        }

        $accessToken = $tokenResponse->json('access_token');

        if (! $accessToken) {
            return redirect()->route('login')->with('error', 'Access token tidak ditemukan dalam respon SiPintu.');
        }

        // 3. Ambil data profil pengguna dari SiPintu Gateway
        try {
            $userResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->get("{$baseUrl}/api/v1/user");
        } catch (\Throwable $e) {
            Log::error('SiPintu SSO Get User Exception: ' . $e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal menghubungi SiPintu untuk data profil.');
        }

        if ($userResponse->failed()) {
            Log::warning('SiPintu SSO User Fetch Failed: ' . $userResponse->body());

            return redirect()->route('login')->with('error', 'Gagal mengambil data akun dari SiPintu Gateway.');
        }

        $sipintuUser = $userResponse->json('data') ?? $userResponse->json();

        if (empty($sipintuUser) || (! isset($sipintuUser['email']) && ! isset($sipintuUser['external_id']))) {
            return redirect()->route('login')->with('error', 'Data pengguna dari SiPintu tidak valid.');
        }

        $isGraduate = filter_var(
            $sipintuUser['graduate'] 
            ?? $sipintuUser['is_graduate'] 
            ?? $sipintuUser['graduated']
            ?? $sipintuUser['is_graduated']
            ?? ($sipintuUser['user']['graduate'] ?? null)
            ?? ($sipintuUser['user']['graduated'] ?? false), 
            FILTER_VALIDATE_BOOLEAN
        );

        if ($isGraduate) {
            return redirect()->route('login')->with('error', 'Akses ditolak: Sistem TE-VAULT hanya dikhususkan bagi siswa & guru aktif SMK.');
        }

        // 4. Auto-Provisioning & Pemetaan User Lokal
        $email = trim((string) ($sipintuUser['email'] ?? ''));
        $externalId = trim((string) ($sipintuUser['external_id'] ?? ($sipintuUser['username'] ?? ($sipintuUser['nis'] ?? ($sipintuUser['nip'] ?? '')))));
        $name = trim((string) ($sipintuUser['name'] ?? 'User'));
        $phone = trim((string) ($sipintuUser['phone'] ?? ($sipintuUser['hp'] ?? ($sipintuUser['no_hp'] ?? '')))) ?: null;
        $classroom = trim((string) ($sipintuUser['classroom'] ?? ($sipintuUser['class_name'] ?? ($sipintuUser['kelas'] ?? '')))) ?: null;
        $roleName = strtolower(trim((string) ($sipintuUser['role'] ?? '')));

        // Urutan Pencocokan Akun (External ID First)
        $user = null;
        if ($externalId !== '') {
            $user = User::where('sipintu_external_id', $externalId)->first();
        }

        if (! $user && $email !== '') {
            $candidate = User::where('email', $email)->first();

            if ($candidate && $candidate->hasAnyRole(['admin', 'super_admin'])) {
                Log::warning("SSO link attempt blocked: external_id={$externalId} mencoba klaim email admin {$email}");

                return redirect()->route('login')->with('error', 'Akun ini tidak dapat ditautkan otomatis via SSO. Hubungi administrator.');
            }

            $user = $candidate;
        }

        // Kunci External ID: Jika akun ditemukan tapi sipintu_external_id sudah terisi dengan ID lain, tolak login
        if ($user && $user->sipintu_external_id !== null && $externalId !== '' && $user->sipintu_external_id !== $externalId) {
            Log::warning("SSO identity conflict: user_id={$user->id} already linked to external_id={$user->sipintu_external_id}, attempted login with external_id={$externalId}");

            return redirect()->route('login')->with('error', 'Konflik identitas akun SSO. Hubungi administrator.');
        }

        // DB Transaction & Catch QueryException
        try {
            DB::transaction(function () use (&$user, $name, $email, $externalId, $phone, $classroom, $roleName) {
                if ($user) {
                    $user->name = $name;
                    if ($email !== '' && $user->email !== $email) {
                        $user->email = $email;
                    }
                    if ($externalId !== '' && empty($user->sipintu_external_id)) {
                        $user->sipintu_external_id = $externalId;
                    }
                    if ($user->email_verified_at === null) {
                        $user->email_verified_at = now();
                    }
                    $user->save();
                } else {
                    $fallbackEmail = $email !== '' ? $email : ($externalId !== '' ? "{$externalId}@smkn1bangsri.sch.id" : 'user_' . Str::random(8) . '@smkn1bangsri.sch.id');

                    $user = User::create([
                        'name'                => $name,
                        'email'               => $fallbackEmail,
                        'sipintu_external_id' => $externalId !== '' ? $externalId : null,
                        'password'            => Hash::make(Str::random(24)),
                        'email_verified_at'   => now(),
                        'is_active'           => true,
                    ]);
                }

                // Sinkronkan relasi profile dan role Spatie (Hanya role operasional: siswa dan guru)
                $isStudent = in_array($roleName, ['student', 'siswa']) || ! empty($classroom) || ($externalId !== '' && ! in_array($roleName, ['teacher', 'guru', 'admin', 'super_admin']));
                $isTeacher = in_array($roleName, ['teacher', 'guru', 'pengajar']);

                if ($isStudent) {
                    if ($externalId !== '' || SiPintuSyncService::isValidPhoneNumber($phone) || $classroom) {
                        $profile = SiswaProfile::firstOrNew(['user_id' => $user->id]);
                        if ($externalId !== '') {
                            $profile->nis = $externalId;
                        }
                        if ($classroom) {
                            $profile->class_name = $classroom;
                        }
                        if (SiPintuSyncService::isValidPhoneNumber($phone)) {
                            $profile->phone = $phone;
                        }
                        $profile->save();
                    }

                    if (! $user->hasAnyRole(['admin', 'super_admin']) && ! $user->hasRole('siswa')) {
                        $user->assignRole('siswa');
                    }
                } elseif ($isTeacher) {
                    if ($externalId !== '' || SiPintuSyncService::isValidPhoneNumber($phone)) {
                        $profile = GuruProfile::firstOrNew(['user_id' => $user->id]);
                        if ($externalId !== '') {
                            $profile->nip = $externalId;
                        }
                        if (SiPintuSyncService::isValidPhoneNumber($phone)) {
                            $profile->phone = $phone;
                        }
                        $profile->save();
                    }

                    if (! $user->hasAnyRole(['admin', 'super_admin']) && ! $user->hasRole('guru')) {
                        $user->assignRole('guru');
                    }
                }
            });
        } catch (QueryException $e) {
            Log::error('SiPintu SSO DB QueryException: ' . $e->getMessage());

            return redirect()->route('login')->with('error', 'Terjadi kesalahan basis data saat sinkronisasi akun. Silakan coba lagi atau hubungi administrator.');
        }

        // 5. Loginkan pengguna ke sesi lokal aplikasi
        Auth::login($user, true);
        $request->session()->regenerate();

        // Open Redirect Guard
        $intended = session('url.intended');
        if ($intended && ! Str::startsWith($intended, url('/'))) {
            session()->forget('url.intended');
        }

        // 6. Langsung arahkan ke Dashboard (Tanpa melihat form login!)
        return redirect()->intended('/dashboard')->with('success', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Menerima payload pembaruan data pengguna realtime dari SiPintu Gateway (Webhook)
     */
    public function syncUser(Request $request): JsonResponse
    {
        // 1. Verifikasi Keamanan Signature HMAC SHA-256
        $signature = $request->header('X-SiPintu-Signature');
        $clientSecret = config('sipintu.client_secret');

        if (blank($clientSecret) || ! is_string($signature) || ! hash_equals(
            hash_hmac('sha256', $request->getContent(), $clientSecret),
            $signature
        )) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $userData = $request->input('user');
        $previous = $request->input('previous', []);

        if (! $userData) {
            return response()->json(['status' => 'error', 'message' => 'Missing user payload'], 400);
        }

        $isGraduate = filter_var(
            $userData['graduate'] 
            ?? $userData['is_graduate'] 
            ?? $userData['graduated'] 
            ?? $userData['is_graduated'] 
            ?? ($userData['user']['graduate'] ?? null) 
            ?? ($userData['user']['graduated'] ?? false), 
            FILTER_VALIDATE_BOOLEAN
        );

        if ($isGraduate) {
            return response()->json([
                'status'  => 'skipped',
                'message' => 'User is graduate/alumni and not eligible for TE-VAULT sync.',
            ]);
        }

        $email = trim((string) ($userData['email'] ?? ''));
        $prevEmail = trim((string) ($previous['email'] ?? ''));
        $externalId = trim((string) ($userData['external_id'] ?? ($userData['username'] ?? ($userData['nis'] ?? ($userData['nip'] ?? '')))));

        // 2. Temukan user berdasarkan email baru, email lama, atau external_id
        $user = null;
        if ($email !== '') {
            $candidate = User::where('email', $email)->first();

            if ($candidate && $candidate->hasAnyRole(['admin', 'super_admin'])) {
                Log::warning("Webhook sync-user blocked: Upaya modifikasi akun admin/super_admin {$email}");

                return response()->json(['status' => 'error', 'message' => 'Cannot overwrite administrative accounts'], 403);
            }

            $user = $candidate;
        }

        if (! $user && $prevEmail !== '') {
            $candidate = User::where('email', $prevEmail)->first();

            if ($candidate && $candidate->hasAnyRole(['admin', 'super_admin'])) {
                Log::warning("Webhook sync-user blocked: Upaya modifikasi akun admin/super_admin {$prevEmail}");

                return response()->json(['status' => 'error', 'message' => 'Cannot overwrite administrative accounts'], 403);
            }

            $user = $candidate;
        }

        if (! $user && $externalId !== '') {
            $siswa = SiswaProfile::where('nis', $externalId)->first();
            if ($siswa) {
                $user = $siswa->user;
            }

            if (! $user) {
                $guru = GuruProfile::where('nip', $externalId)->first();
                if ($guru) {
                    $user = $guru->user;
                }
            }

            if ($user && $user->hasAnyRole(['admin', 'super_admin'])) {
                Log::warning("Webhook sync-user blocked: Upaya modifikasi akun admin/super_admin via external_id {$externalId}");

                return response()->json(['status' => 'error', 'message' => 'Cannot overwrite administrative accounts'], 403);
            }
        }

        // 3. Siapkan data pembaruan
        $updateFields = [
            'name'  => $userData['name'] ?? 'User',
            'email' => $email ?: ($user?->email ?? ($externalId ? "{$externalId}@smkn1bangsri.sch.id" : 'user_' . Str::random(8) . '@smkn1bangsri.sch.id')),
        ];

        // 4. Update jika user sudah ada, atau buat baru
        if ($user) {
            $user->update($updateFields);
            $action = 'updated';
        } else {
            $updateFields['email_verified_at'] = now();
            $updateFields['is_active'] = true;
            $updateFields['password'] = Hash::make(Str::random(24));
            $user = User::create($updateFields);
            $action = 'created';
        }

        // 5. Sinkronkan profil Siswa / Guru & role
        $roleName = strtolower(trim((string) ($userData['role'] ?? '')));
        $classroom = trim((string) ($userData['classroom'] ?? ($userData['class_name'] ?? ($userData['kelas'] ?? '')))) ?: null;
        $phone = trim((string) ($userData['phone'] ?? ($userData['hp'] ?? ($userData['no_hp'] ?? '')))) ?: null;

        $isStudent = in_array($roleName, ['student', 'siswa']) || ! empty($classroom) || ($externalId !== '' && ! in_array($roleName, ['teacher', 'guru', 'admin', 'super_admin']));
        $isTeacher = in_array($roleName, ['teacher', 'guru', 'pengajar']);

        if ($isStudent) {
            if ($externalId !== '' || SiPintuSyncService::isValidPhoneNumber($phone) || $classroom) {
                $profile = SiswaProfile::firstOrNew(['user_id' => $user->id]);
                if ($externalId !== '') {
                    $profile->nis = $externalId;
                }
                if ($classroom) {
                    $profile->class_name = $classroom;
                }
                if (SiPintuSyncService::isValidPhoneNumber($phone)) {
                    $profile->phone = $phone;
                }
                $profile->save();
            }

            if (! $user->hasAnyRole(['admin', 'super_admin']) && ! $user->hasRole('siswa')) {
                $user->assignRole('siswa');
            }
        } elseif ($isTeacher) {
            if ($externalId !== '' || SiPintuSyncService::isValidPhoneNumber($phone)) {
                $profile = GuruProfile::firstOrNew(['user_id' => $user->id]);
                if ($externalId !== '') {
                    $profile->nip = $externalId;
                }
                if (SiPintuSyncService::isValidPhoneNumber($phone)) {
                    $profile->phone = $phone;
                }
                $profile->save();
            }

            if (! $user->hasAnyRole(['admin', 'super_admin']) && ! $user->hasRole('guru')) {
                $user->assignRole('guru');
            }
        }

        return response()->json([
            'status'  => 'success',
            'action'  => $action,
            'message' => "User {$user->email} berhasil disinkronkan di aplikasi downstream.",
            'user_id' => $user->id,
        ]);
    }
}
