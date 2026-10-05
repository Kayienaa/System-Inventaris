<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsFilled
{
    /**
     * Daftar route yang dikecualikan dari pengalihan wajib nomor HP.
     *
     * @var list<string>
     */
    protected array $exceptRoutes = [
        'complete-phone',
        'complete-phone.store',
        'logout',
        'verification.notice',
        'verification.send',
        'verification.verify',
        'password.confirm',
        'password.update',
        'storage.local',
        'oauth.callback',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // L3: Lewati pengecekan untuk API / JSON clients agar tidak menerima
        // HTML redirect ke /complete-phone alih-alih JSON error response.
        if ($request->expectsJson() || $request->is('api/*')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && $user->hasAnyRole(['siswa', 'guru'])) {
            $phone = trim((string) ($user->siswaProfile?->phone ?? $user->guruProfile?->phone ?? ''));

            // Hanya cegat dan alihkan pengguna ke /complete-phone jika kolom phone bernilai NULL atau kosong
            if ($phone === '') {
                if ($request->routeIs($this->exceptRoutes)) {
                    return $next($request);
                }

                return redirect()->route('complete-phone');
            }
        }

        return $next($request);
    }
}
