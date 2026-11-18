<?php

namespace App\Services;

use App\Models\EventoActividad;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RegistrarActividad
{
    public static function registrar(string $evento, ?Model $recurso = null, array $detalles = [], ?User $usuario = null, bool $usarContextoSesion = true): void
    {
        $usuario ??= auth()->user();
        if (! $usuario) {
            return;
        }

        $sedeId = $recurso instanceof Sede ? $recurso->id : $recurso?->getAttribute('sede_id');
        if (! $sedeId && $usarContextoSesion && request()->hasSession()) {
            $sede = request()->session()->get('sede');
            $sedeId = $sede instanceof Sede ? $sede->id : (is_numeric($sede) ? (int) $sede : null);
        }
        $empresaId = $sedeId ? Sede::whereKey($sedeId)->value('empresa_id') : null;
        if ($usarContextoSesion) {
            $empresaId ??= $usuario->empleado?->empresa_id;
        }

        EventoActividad::create([
            'usuario_id' => $usuario->id,
            'usuario_nombre' => $usuario->name,
            'evento' => $evento,
            'recurso_tipo' => $recurso ? class_basename($recurso) : null,
            'recurso_id' => $recurso?->getKey(),
            'empresa_id' => $empresaId,
            'sede_id' => $sedeId,
            // Solo identificadores, nombres de campos y cantidades; nunca valores clínicos.
            'detalles' => $detalles ?: null,
            'ocurrio_en' => now('UTC'),
        ]);
    }
}
