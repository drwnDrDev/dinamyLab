<?php

namespace App\Observers;

use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Services\RegistrarActividad;
use Illuminate\Database\Eloquent\Model;

class ActividadObserver
{
    public function created(Model $modelo): void
    {
        if (! auth()->check()) {
            return;
        }

        if ($modelo instanceof Persona) {
            RegistrarActividad::registrar('paciente.creado', $modelo);
        } elseif ($modelo instanceof Orden) {
            RegistrarActividad::registrar('orden.creada', $modelo);
        } elseif ($modelo instanceof Resultado) {
            $procedimiento = Procedimiento::find($modelo->procedimiento_id);
            RegistrarActividad::registrar('resultado.creado', $procedimiento, [
                'resultado_id' => $modelo->id,
                'parametro_id' => $modelo->parametro_id,
                'orden_id' => $procedimiento?->orden_id,
            ]);
        }
    }
}
