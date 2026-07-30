<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->input('login_as', 'admin')) {
            'guru' => [
                'nip' => ['required', 'string'],
                'password' => ['required', 'string'],
            ],
            'siswa' => [
                'nis' => ['required', 'string'],
                'tanggal_lahir' => ['required', 'date_format:Y-m-d'],
            ],
            default => [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ],
        };
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $role = $this->input('login_as', 'admin');

        $credentials = match ($role) {
            'guru' => [
                'nip' => $this->input('nip'),
                'password' => $this->input('password'),
                'role' => 'guru',
            ],
            'siswa' => [
                'nis' => $this->input('nis'),
                // "password" siswa = tanggal lahir mereka sendiri
                'password' => $this->input('tanggal_lahir'),
                'role' => 'siswa',
            ],
            default => [
                'email' => $this->input('email'),
                'password' => $this->input('password'),
                'role' => 'admin',
            ],
        };

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Data yang Anda masukkan tidak cocok dengan data kami.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        $identifier = $this->input('email') ?? $this->input('nip') ?? $this->input('nis') ?? 'unknown';

        return Str::transliterate(Str::lower($identifier).'|'.$this->ip());
    }
}