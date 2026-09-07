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
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasAnyRole(['siswa', 'guru'])) {
            $phone = $user->siswaProfile?->phone ?: $user->guruProfile?->phone;

            // Jika nomor HP kosong atau baris profil belum ada di database
            if (empty($phone)) {
                if ($request->routeIs($this->exceptRoutes)) {
                    return $next($request);
                }

                return redirect()->route('complete-phone');
            }
        }

        return $next($request);
    }
}
