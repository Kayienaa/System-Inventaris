<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsFilled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasAnyRole(['siswa', 'guru'])) {
            $hasProfile = $user->siswaProfile !== null || $user->guruProfile !== null;

            if ($hasProfile) {
                $phone = $user->siswaProfile?->phone ?: $user->guruProfile?->phone;

                if (empty($phone)) {
                    if ($request->routeIs('complete-phone', 'complete-phone.store', 'logout')) {
                        return $next($request);
                    }

                    return redirect()->route('complete-phone');
                }
            }
        }

        return $next($request);
    }
}
