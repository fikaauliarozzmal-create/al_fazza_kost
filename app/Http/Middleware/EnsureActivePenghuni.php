<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActivePenghuni
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user?->role === 'User'
                && $user->status_akun === 'Aktif'
                && $user->penghuni()->where('status_penghuni', 'Aktif')->exists(),
            403
        );

        return $next($request);
    }
}
