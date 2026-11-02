<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Impide que una instancia atienda peticiones dirigidas al dominio de otra.
 *
 * Sin APP_INSTANCE configurado la guarda no actúa (desarrollo y tests).
 * Con instancia pero sin dominios declarados solo falla en producción.
 */
class EnsureInstance
{
    public function handle(Request $request, Closure $next): Response
    {
        $actual = config('instancias.actual');
        $hosts = config('instancias.hosts', []);

        if (blank($actual)) {
            return $next($request);
        }

        if ($hosts === []) {
            abort_if(app()->isProduction(), 503, 'Instancia sin dominios configurados.');

            return $next($request);
        }

        abort_unless(
            in_array($request->getHost(), $hosts, true),
            503,
            'Instancia no habilitada para este dominio.'
        );

        return $next($request);
    }
}
