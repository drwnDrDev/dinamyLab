<?php

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
        // Sanctum ya aplica EncryptCookies, StartSession, CSRF y AuthenticateSession
        // en peticiones stateful. No agregues EncryptCookies aquí: desencriptar dos
        // veces invalida la cookie de sesión y el usuario vuelve al login.
        // StartSession se mantiene para los controladores de la API que leen la
        // sesión (p. ej. la sede elegida) en peticiones no stateful.
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ]);

        // Aísla cada instancia: una petición dirigida al dominio de otro
        // cliente se rechaza con 503 (ver config/instancias.php).
        $middleware->web(append: [
            \App\Http\Middleware\EnsureInstance::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\EnsureInstance::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

