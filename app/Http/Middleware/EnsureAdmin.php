<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->user()?->role, ['Admin', 'Super Admin'], true)) {
            return $next($request);
        }

        if ($request->user()?->role === 'User') {
            return redirect()->route('landing');
        }

        abort(403);
    }
}
