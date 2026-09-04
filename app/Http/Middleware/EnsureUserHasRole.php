<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Contoh pemakaian di route: ->middleware('role:admin,guru') atau ->middleware('role:siswa|guru')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $flattenedRoles = [];
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $flattenedRoles[] = $trimmed;
                }
            }
        }

        if (! $user || ! $user->hasAnyRole($flattenedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}