<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pembatasan akses berbasis peran (RBAC). Pemakaian di route:
 * ->middleware(EnsureRole::class . ':Admin')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $namaRole = $user?->role?->nama_role;

        if (! $user || ! $user->aktif || ! in_array($namaRole, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}