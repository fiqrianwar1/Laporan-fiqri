<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Batasi akses hanya untuk role tertentu.
     * Contoh pemakaian di route: ->middleware('role:tinter')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Kamu tidak punya akses untuk mengisi laporan.');
        }

        return $next($request);
    }
}
