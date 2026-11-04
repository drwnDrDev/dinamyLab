<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orden;
use App\Models\Procedimiento;
use App\Services\ControlSede;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        $sede = $usuario->hasRole('admin')
            ? $request->session()->get('sede')
            : ControlSede::autorizarSedeActiva($request);
        $empleado = \App\Models\Empleado::where('user_id', $usuario->id)->first();
        $ordenes = $usuario->hasRole('admin')
            ? Orden::all()
            : Orden::where('sede_id', $sede->id)->get();
        $procedimientos = $usuario->hasRole('admin')
            ? Procedimiento::all()
            : Procedimiento::where('sede_id', $sede->id)->get();
        $procedimientosByExamen = $procedimientos->groupBy('examen_id')->map(function ($group) {
            return [
                'examen' => $group->first()->examen->nombre,
                'count' => $group->count(),
            ];
        })->sortByDesc('count')->values();

        $procedimientosByEstado = $procedimientos->groupBy('estado')->map->count();

        $pacientesHoy = $procedimientos->unique(function ($item) {
            return $item->orden->paciente_id;
        })->count();

        return response()->json([
            'ordenes' => $ordenes,
            'empleado' => $empleado,
            'procedimientosByExamen' => $procedimientosByExamen,
            'procedimientosByEstado' => $procedimientosByEstado,
            'pacientesHoy' => $pacientesHoy
        ]);
    }

}
