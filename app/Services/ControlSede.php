<?php

namespace App\Services;

use App\Models\Empleado;
use App\Models\Procedimiento;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Control de membresía de sede (C-05).
 *
 * La sede activa guardada en sesión no demuestra por sí sola que el usuario
 * pertenezca a ella: siempre se verifica contra empleado_sede.
 */
class ControlSede
{
    public static function pertenece(?Empleado $empleado, Sede|int|null $sede): bool
    {
        if (! $empleado || ! $sede) {
            return false;
        }

        $sedeId = $sede instanceof Sede ? $sede->id : $sede;

        return $empleado->sedes()->whereKey($sedeId)->exists();
    }

    /**
     * Aborta con 403 si el usuario no puede elegir la sede.
     */
    public static function autorizarEleccion(Request $request, Sede $sede): void
    {
        $empleado = $request->user()?->empleado;

        if (! self::pertenece($empleado, $sede)) {
            self::registrarDenegacion($request, 'elegir_sede', ['sede_id' => $sede->id]);
            abort(403, 'No pertenece a esta sede.');
        }
    }

    /**
     * Aborta con 403 si no hay sede activa, si el empleado no pertenece a ella
     * o si el procedimiento es de otra sede.
     */
    public static function autorizarProcedimiento(Request $request, Procedimiento $procedimiento): void
    {
        $sede = $request->session()->get('sede');
        $empleado = $request->user()?->empleado;

        $autorizado = $sede
            && (int) $procedimiento->sede_id === (int) $sede->id
            && self::pertenece($empleado, $sede);

        if (! $autorizado) {
            self::registrarDenegacion($request, 'acceso_resultados', [
                'procedimiento_id' => $procedimiento->id,
                'sede_activa_id' => $sede?->id,
            ]);
            abort(403, 'No tiene acceso a los resultados de esta sede.');
        }
    }

    private static function registrarDenegacion(Request $request, string $accion, array $contexto): void
    {
        // Solo IDs: nunca datos del paciente.
        Log::warning('Acceso denegado por control de sede.', [
            'usuario_id' => $request->user()?->id,
            'accion' => $accion,
            ...$contexto,
        ]);
    }
}
