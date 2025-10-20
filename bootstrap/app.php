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

        // 1. Configura el grupo de middleware 'api'
        $middleware->api(prepend: [
            // Este es el middleware clave de Sanctum para habilitar sesiones
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // Los siguientes son a menudo necesarios para que la sesión funcione correctamente en API
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ]);

        // Si necesitas que otros middlewares del grupo 'web' también estén en 'api',
        // podrías considerarlos, pero generalmente con los de arriba es suficiente

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// NOTA: Esta línea '$app->singleton(...)' está fuera de la función de configuración
// y es incorrecta en Laravel 12. Asumo que es un error de copia.
// La configuración del Kernel está implícita en 'Application::configure'.

// $app->singleton(
// Illuminate\Contracts\Console\Kernel::class,
// App\Console\Kernel::class
// );
