<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Faire confiance au proxy Render (HTTPS)
        $middleware->trustProxies(at: '*');

        // 1. Redirection personnalisée après connexion
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );

        // 2. On conserve tes alias de middleware
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 3. On conserve tes exceptions CSRF pour l'API
        $middleware->preventRequestForgery(except: [
            'api/*',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();