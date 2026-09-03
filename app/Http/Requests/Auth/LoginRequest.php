<?php

namespace App\Http\Requests\Auth;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim((string) $this->input('email'));
        $password = (string) $this->input('password');

        $user = null;

        if (str_contains($login, '@')) {
            // Jika input mengandung @ (format email): cari langsung di kolom email tabel users
            $user = User::where('email', $login)->first();
            if (! $user) {
                $user = User::whereRaw('LOWER(email) = ?', [strtolower($login)])->first();
            }
        } elseif (ctype_digit($login)) {
            // Jika input berupa angka murni (tanpa @):
            // 1. Cek kecocokan dengan kolom nip pada tabel guru_profiles
            $guruProfile = GuruProfile::where('nip', $login)->first();
            if ($guruProfile && $guruProfile->user) {
                $user = $guruProfile->user;
            } else {
                // 2. Cek kecocokan dengan kolom nis pada tabel siswa_profiles
                $siswaProfile = SiswaProfile::where('nis', $login)->first();
                if ($siswaProfile && $siswaProfile->user) {
                    $user = $siswaProfile->user;
                }
            }
        }

        // Verifikasi keberadaan user dan kecocokan hash password
        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
