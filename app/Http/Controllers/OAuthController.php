<?php

namespace App\Http\Controllers;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use App\Services\SiPintuService;
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
    protected SiPintuService $sipintuService;

    public function __construct(SiPintuService $sipintuService)
    {
        $this->sipintuService = $sipintuService;
    }

    /**
     * Menerima otorisasi SSO otomatis dari Portal SiPintu Gateway
     */
    public function callback(Request $request): RedirectResponse
    {
        // 1. Validasi error dari SiPintu Gateway
        if ($request->has('error')) {
            $errorDesc = $request->input('error_description', $request->input('error'));
            Log::warning("SiPintu SSO Callback error: {$errorDesc}");

            return redirect()->route('login')->with('error', 'Otorisasi SSO SiPintu dibatalkan atau gagal.');
        }

        // 2. Tangkap Authorization Code yang dikirim SiPintu
        $code = $request->input('code');

        if (! $code) {
            return redirect()->route('login')->with('error', 'Otorisasi SSO SiPintu gagal: Kode otorisasi tidak ditemukan.');
        }

        // 3. Verifikasi state dan sipintu_verifier jika sesi dimulai dari SITEFA
        $expectedState = session('sipintu_state') ?? session('oauth_state');
        if ($expectedState) {
            $state = $request->input('state');
            if (! $state || ! hash_equals((string) $expectedState, (string) $state)) {
                session()->forget(['sipintu_state', 'oauth_state', 'sipintu_verifier', 'oauth_code_verifier']);

                return redirect()->route('login')->with('error', 'Validasi sesi SSO (state) tidak cocok. Silakan coba lagi.');
            }
        }

        $codeVerifier = session('sipintu_verifier') ?? session('oauth_code_verifier');
        session()->forget(['sipintu_state', 'oauth_state', 'sipintu_verifier', 'oauth_code_verifier']);

        // 4. Replay Protection (Single-Use Code)
        $codeHash = hash('sha256', $code);
        if (! Cache::add("sso_code:{$codeHash}", true, now()->addMinutes(2))) {
            Log::warning('SSO authorization code replay terdeteksi.');

            return redirect()->route('login')->with('error', 'Kode otorisasi sudah digunakan. Silakan login ulang.');
        }

        // 5. Tukar Code dengan Access Token (Backend-to-Backend HTTP POST)
        $tokenData = $this->sipintuService->exchangeCode($code, $codeVerifier);
        if (! $tokenData || empty($tokenData['access_token'])) {
            return redirect()->route('login')->with('error', 'Gagal memverifikasi token ke SiPintu Gateway.');
        }

        $accessToken = $tokenData['access_token'];

        // 6. Ambil data profil pengguna dari SiPintu Gateway
        $sipintuUser = $this->sipintuService->fetchUser($accessToken);

        if (empty($sipintuUser) || (! isset($sipintuUser['email']) && ! isset($sipintuUser['external_id']))) {
            return redirect()->route('login')->with('error', 'Data pengguna dari SiPintu tidak valid.');
        }

        // 7. Tolak jika status akun bukan active atau pengguna adalah alumni
        $isActive = filter_var(
            $sipintuUser['is_active']
            ?? ($sipintuUser['user']['is_active'] ?? null)
            ?? ($sipintuUser['status'] ?? true),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($isActive === false || (isset($sipintuUser['status']) && in_array(strtolower((string) $sipintuUser['status']), ['inactive', 'nonactive', 'banned', 'suspended', '0']))) {
            return redirect()->route('login')->with('error', 'Akun SiPintu Anda dalam status nonaktif.');
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

        // 8. Ekstraksi Informasi Identitas
        $email = trim((string) ($sipintuUser['email'] ?? ''));
        $externalId = trim((string) ($sipintuUser['external_id'] ?? ($sipintuUser['username'] ?? ($sipintuUser['nis'] ?? ($sipintuUser['nip'] ?? '')))));
        $name = trim((string) ($sipintuUser['name'] ?? 'User'));
        $phone = trim((string) (
            $sipintuUser['phone']
            ?? $sipintuUser['hp']
            ?? $sipintuUser['no_hp']
            ?? $sipintuUser['telepon']
            ?? ($sipintuUser['user']['phone'] ?? null)
            ?? ($sipintuUser['user']['hp'] ?? null)
            ?? ''
        )) ?: null;
        $avatar = $sipintuUser['avatar']
            ?? $sipintuUser['photo']
            ?? $sipintuUser['foto']
            ?? ($sipintuUser['user']['avatar'] ?? null)
            ?? ($sipintuUser['user']['foto'] ?? null)
            ?? null;
        $classroom = trim((string) ($sipintuUser['classroom'] ?? ($sipintuUser['class_name'] ?? ($sipintuUser['kelas'] ?? '')))) ?: null;
        $roleName = strtolower(trim((string) ($sipintuUser['role'] ?? ($sipintuUser['user']['role'] ?? ''))));

        // 9. Resolusi User: Utamakan sipintu_external_id. Fallback email HANYA untuk first-time link akun non-privileged
        $user = null;
        if ($externalId !== '') {
            $user = User::where('sipintu_external_id', $externalId)->first();
        }

        if (! $user && $email !== '') {
            $candidate = User::where('email', $email)->first();

            if ($candidate) {
                // Tolak jika target akun memiliki role admin / super_admin
                if ($candidate->hasAnyRole(['admin', 'super_admin'])) {
                    Log::warning("SSO ditolak untuk akun staf administratif user_id={$candidate->id}");

                    return redirect()->route('login')->with('error', 'Akun administratif wajib login menggunakan kredensial internal.');
                }

                // Fallback pencocokan email HANYA untuk first-time link akun yang belum punya sipintu_external_id
                if ($candidate->sipintu_external_id === null) {
                    $user = $candidate;
                } else {
                    Log::warning("SSO identity conflict: user_id={$candidate->id} already linked to external_id={$candidate->sipintu_external_id}, attempted login with external_id={$externalId}");

                    return redirect()->route('login')->with('error', 'Konflik identitas akun SSO. Hubungi administrator.');
                }
            }
        }

        // Verifikasi izin: Akun administratif dilarang login via alur SSO
        if ($user?->hasAnyRole(['admin', 'super_admin'])) {
            Log::warning("SSO ditolak untuk akun staf administratif user_id={$user->id}");

            return redirect()->route('login')->with('error', 'Akun administratif wajib login menggunakan kredensial internal.');
        }

        // Kunci External ID: Jika akun ditemukan tapi sipintu_external_id sudah terisi dengan ID lain, tolak
        if ($user && $user->sipintu_external_id !== null && $externalId !== '' && $user->sipintu_external_id !== $externalId) {
            Log::warning("SSO identity conflict: user_id={$user->id} already linked to external_id={$user->sipintu_external_id}, attempted login with external_id={$externalId}");

            return redirect()->route('login')->with('error', 'Konflik identitas akun SSO. Hubungi administrator.');
        }

        // Tolak jika email dari SSO bentrok dengan akun admin/super_admin internal SITEFA
        if ($email !== '') {
            $adminCollision = User::where('email', $email)
                ->where(function ($q) use ($user) {
                    if ($user) {
                        $q->where('id', '!=', $user->id);
                    }
                })
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin']))
                ->exists();

            if ($adminCollision) {
                Log::warning("SSO email collision with admin account: {$email}");

                return redirect()->route('login')->with('error', 'Akun administratif wajib login menggunakan kredensial internal.');
            }
        }

        // 10. DB Transaction & Catch QueryException
        try {
            DB::transaction(function () use (&$user, $name, $email, $externalId, $phone, $classroom, $roleName, $avatar, $sipintuUser) {
                if ($user) {
                    $user->name = $name;
                    if ($email !== '' && $user->email !== $email) {
                        if (! User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
                            $user->email = $email;
                        }
                    }
                    if ($externalId !== '' && empty($user->sipintu_external_id)) {
                        $user->sipintu_external_id = $externalId;
                    }
                    if ($user->email_verified_at === null) {
                        $user->email_verified_at = now();
                    }
                    if (! empty($avatar)) {
                        $user->avatar = $avatar;
                    }
                    $user->save();
                } else {
                    $fallbackEmail = $email !== '' ? $email : ($externalId !== '' ? "{$externalId}@smkn1bangsri.sch.id" : 'user_' . Str::random(8) . '@smkn1bangsri.sch.id');

                    $user = User::create([
                        'name'                => $name,
                        'email'               => $fallbackEmail,
                        'sipintu_external_id' => $externalId !== '' ? $externalId : null,
                        'avatar'              => ! empty($avatar) ? $avatar : null,
                        'password'            => Hash::make(Str::random(32)),
                        'email_verified_at'   => now(),
                        'is_active'           => true,
                    ]);
                }

                // Sinkronkan relasi profile dan role Spatie (Hanya role operasional: siswa dan guru)
                $mappedRole = $this->sipintuService->localRole($sipintuUser);
                $isStudent = ($mappedRole === 'siswa') || ! empty($classroom) || ($externalId !== '' && ! in_array($roleName, ['teacher', 'guru', 'admin', 'super_admin']));
                $isTeacher = ($mappedRole === 'guru');

                if (str_contains($roleName, 'admin')) {
                    $isStudent = false;
                    $isTeacher = false;
                }

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

        // 11. Loginkan pengguna: remember=false demi keamanan PC lab bersama
        Auth::login($user, false);
        $request->session()->regenerate();

        // Open Redirect Guard
        $intended = session('url.intended');
        if ($intended && ! Str::startsWith($intended, url('/'))) {
            session()->forget('url.intended');
        }

        // 12. Langsung arahkan ke Dashboard
        return redirect()->intended('/dashboard')->with('success', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Menerima payload pembaruan data pengguna realtime dari SiPintu Gateway (Webhook)
     */
    public function syncUser(Request $request): JsonResponse
    {
        $userData = $request->input('user');

        if (! is_array($userData)) {
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

        $externalId = trim((string) ($userData['external_id'] ?? ($userData['username'] ?? ($userData['nis'] ?? ($userData['nip'] ?? '')))));
        $email = trim((string) ($userData['email'] ?? ''));

        // Cari user HANYA berdasarkan sipintu_external_id (dan legacy profile mapping jika belum terisi)
        $user = null;
        if ($externalId !== '') {
            $user = User::where('sipintu_external_id', $externalId)->first();

            if (! $user) {
                $legacyUser = SiswaProfile::where('nis', $externalId)->first()?->user 
                    ?? GuruProfile::where('nip', $externalId)->first()?->user;

                if ($legacyUser && empty($legacyUser->sipintu_external_id) && ! $legacyUser->hasAnyRole(['admin', 'super_admin'])) {
                    $legacyUser->sipintu_external_id = $externalId;
                    $legacyUser->save();
                    $user = $legacyUser;
                }
            }
        }

        // Jika akun target memiliki role admin atau super_admin -> Return HTTP 403 Forbidden
        if ($user && $user->hasAnyRole(['admin', 'super_admin'])) {
            Log::warning("Webhook sync-user blocked: Upaya modifikasi akun admin/super_admin via external_id {$externalId}");

            return response()->json(['status' => 'error', 'message' => 'Cannot overwrite administrative accounts'], 403);
        }

        // Tolak jika email bertabrakan dengan akun admin/super_admin internal
        if ($email !== '') {
            $adminCollision = User::where('email', $email)
                ->where(function ($q) use ($user) {
                    if ($user) {
                        $q->where('id', '!=', $user->id);
                    }
                })
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin']))
                ->exists();

            if ($adminCollision) {
                Log::warning("Webhook sync-user blocked: Upaya modifikasi akun admin/super_admin {$email}");

                return response()->json(['status' => 'error', 'message' => 'Cannot overwrite administrative accounts'], 403);
            }
        }

        $name = trim((string) ($userData['name'] ?? 'User'));
        $phone = trim((string) (
            $userData['phone']
            ?? $userData['hp']
            ?? $userData['no_hp']
            ?? $userData['telepon']
            ?? ($userData['user']['phone'] ?? null)
            ?? ($userData['user']['hp'] ?? null)
            ?? ''
        )) ?: null;
        $classroom = trim((string) ($userData['classroom'] ?? ($userData['class_name'] ?? ($userData['kelas'] ?? '')))) ?: null;
        $avatar = $userData['avatar']
            ?? $userData['photo']
            ?? $userData['foto']
            ?? ($userData['user']['avatar'] ?? null)
            ?? ($userData['user']['foto'] ?? null)
            ?? null;

        if ($user) {
            // Update nama dan email aman (Role lokal SITEFA TIDAK PERNAH diubah oleh webhook)
            $user->name = $name;
            if ($email !== '' && $user->email !== $email) {
                if (! User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
                    $user->email = $email;
                }
            }
            if (! empty($avatar)) {
                $user->avatar = $avatar;
            }
            $user->save();

            // Update profil data sekolah tanpa mengubah role Spatie yang sudah ada
            if ($user->hasRole('siswa')) {
                $profile = SiswaProfile::firstOrNew(['user_id' => $user->id]);
                if ($externalId !== '') $profile->nis = $externalId;
                if ($classroom) $profile->class_name = $classroom;
                if (SiPintuSyncService::isValidPhoneNumber($phone)) $profile->phone = $phone;
                $profile->save();
            } elseif ($user->hasRole('guru')) {
                $profile = GuruProfile::firstOrNew(['user_id' => $user->id]);
                if ($externalId !== '') $profile->nip = $externalId;
                if (SiPintuSyncService::isValidPhoneNumber($phone)) $profile->phone = $phone;
                $profile->save();
            }

            $action = 'updated';
        } else {
            // Pembuatan akun baru jika belum ada
            $fallbackEmail = $email !== '' ? $email : ($externalId !== '' ? "{$externalId}@smkn1bangsri.sch.id" : 'user_' . Str::random(8) . '@smkn1bangsri.sch.id');

            $user = User::create([
                'name'                => $name,
                'email'               => $fallbackEmail,
                'sipintu_external_id' => $externalId !== '' ? $externalId : null,
                'avatar'              => ! empty($avatar) ? $avatar : null,
                'password'            => Hash::make(Str::random(32)),
                'email_verified_at'   => now(),
                'is_active'           => true,
            ]);

            // Whitelist role strictly untuk pengguna baru
            $mappedRole = $this->sipintuService->localRole($userData);
            $rawRole = strtolower(trim((string) ($userData['role'] ?? '')));

            if (! str_contains($rawRole, 'admin')) {
                if ($mappedRole === 'siswa' || ! empty($classroom) || ($externalId !== '' && $mappedRole !== 'guru')) {
                    $user->assignRole('siswa');
                    $profile = SiswaProfile::firstOrNew(['user_id' => $user->id]);
                    if ($externalId !== '') $profile->nis = $externalId;
                    if ($classroom) $profile->class_name = $classroom;
                    if (SiPintuSyncService::isValidPhoneNumber($phone)) $profile->phone = $phone;
                    $profile->save();
                } elseif ($mappedRole === 'guru') {
                    $user->assignRole('guru');
                    $profile = GuruProfile::firstOrNew(['user_id' => $user->id]);
                    if ($externalId !== '') $profile->nip = $externalId;
                    if (SiPintuSyncService::isValidPhoneNumber($phone)) $profile->phone = $phone;
                    $profile->save();
                }
            }

            $action = 'created';
        }

        // Jika changed_fields memuat 'password': panggil onPasswordChanged
        $changedFields = $request->input('changed_fields', []);
        if (! is_array($changedFields)) {
            $changedFields = [$changedFields];
        }

        $passwordHash = (string) (
            $userData['password_hash'] 
            ?? $userData['password'] 
            ?? $request->input('password_hash') 
            ?? $request->input('password') 
            ?? ''
        );

        if (in_array('password', $changedFields) || $passwordHash !== '') {
            $this->onPasswordChanged($user, $passwordHash);
        }

        return response()->json([
            'status'  => 'success',
            'action'  => $action,
            'message' => "User {$user->email} berhasil disinkronkan di aplikasi downstream.",
            'user_id' => $user->id,
        ]);
    }

    /**
     * Menerima endpoint sinkronisasi password (Fallback Ack) dari SiPintu Gateway
     */
    public function syncPassword(Request $request): JsonResponse
    {
        $externalId = trim((string) (
            $request->input('external_id') 
            ?? $request->input('user.external_id') 
            ?? $request->input('username') 
            ?? $request->input('nis') 
            ?? $request->input('nip') 
            ?? ''
        ));

        $hash = (string) (
            $request->input('password_hash') 
            ?? $request->input('password') 
            ?? $request->input('user.password_hash') 
            ?? $request->input('user.password') 
            ?? ''
        );

        $user = null;
        if ($externalId !== '') {
            $user = User::where('sipintu_external_id', $externalId)->first();
        }

        if ($user) {
            if ($user->hasAnyRole(['admin', 'super_admin'])) {
                Log::warning("syncPassword blocked for administrative account user_id={$user->id}");

                return response()->json(['status' => 'error', 'message' => 'Cannot modify administrative accounts'], 403);
            }

            $this->onPasswordChanged($user, $hash);
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'SiPintu password sync is acknowledged',
        ], 200);
    }

    /**
     * Penanganan perubahan password dari SiPintu:
     * - Rotasi remember_token
     * - Hapus session DB hanya jika SESSION_DRIVER=database
     * - Hanya simpan password hash jika accept_password_hash=true dan format valid bcrypt $2y$
     */
    private function onPasswordChanged(User $user, string $hash): void
    {
        $updates = [
            'remember_token' => Str::random(60),
        ];

        $acceptHash = (bool) config('services.sipintu.accept_password_hash', false);
        $isValidBcrypt = (bool) preg_match('/^\$2y\$\d{2}\$[A-Za-z0-9\.\/]{53}$/', $hash);

        if ($acceptHash && $isValidBcrypt) {
            $updates['password'] = $hash;
        }

        $user->forceFill($updates)->saveQuietly();

        if (config('session.driver') === 'database') {
            try {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            } catch (\Throwable $e) {
                Log::warning('Failed to purge database sessions for user ' . $user->id . ': ' . $e->getMessage());
            }
        }
    }
}
