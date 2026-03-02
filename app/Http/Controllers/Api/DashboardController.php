<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $sede = $request->session()->get('sede');
        $usuario = Auth()->user();
        $empleado = \App\Models\Empleado::where('user_id', $usuario->id)->first();
        $ordenes = \App\Models\Orden::all();
        $procedimientos = $usuario->hasRole('administrador') ? \App\Models\Procedimiento::all() :  \App\Models\Procedimiento::where('sede_id', $sede->id)->get();
        $procedimientosByExamen = $procedimientos->groupBy('examen_id')->map(function ($group) {
            return [
                'examen' => $group->first()->examen->nombre,
                'count' => $group->count(),
            ];
        })->sortByDesc('count')->values();

        $procedimientosByEstado = $procedimientos->where('estado', 'en proceso')->groupBy('estado')->map(function ($group) {
            return [
                'estado' => $group->first()->estado,
                'count' => $group->count(),
            ];
        });
        $pacientesHoy = $procedimientos->unique('persona_id')->count();

        return response()->json([
            'ordenes' => $ordenes,
            'empleado' => $empleado,
            'procedimientosByExamen' => $procedimientosByExamen,
            'procedimientosByEstado' => $procedimientosByEstado,
            'pacientesHoy' => $pacientesHoy
        ]);
    }

}
