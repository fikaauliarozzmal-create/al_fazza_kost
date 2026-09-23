<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // DANA sends a signed server-to-server notification, not a browser form.
        $middleware->validateCsrfTokens(except: ['dana/webhook/finish']);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'user' => \App\Http\Middleware\EnsureUser::class,
            'penghuni.active' => \App\Http\Middleware\EnsureActivePenghuni::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
