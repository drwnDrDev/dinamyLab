<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\TipoDocumento;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empleado $empleado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empleado $empleado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empleado $empleado)
    {
        //
    }

    public function dashboard()
    {
        $usuario = auth()->user();
        $empleado= Empleado::where('user_id',$usuario->id)->first();
        $procedimientos = \App\Models\Procedimiento::all();
            $procedimientosByExamen = $procedimientos->groupBy('examen_id')->map(function ($group) {
                return [
                    'examen' => $group->first()->examen->nombre,
                    'count' => $group->count(),
                ];
            });
        $procedimientosByEstado = $procedimientos->groupBy('estado')->map(function ($group) {
            return [
                'estado' => $group->first()->estado,
                'count' => $group->count(),
            ];
        });
        $pacientesHoy = $procedimientos->where('fecha', now()->toDateString())->groupBy('persona_id')->count();

        return view('dashboard',compact('empleado','procedimientos','procedimientosByExamen','procedimientosByEstado','pacientesHoy'));
    }

    public function select()
    {
        $usuario = auth()->user();
        $empleado= Empleado::where('user_id',$usuario->id)->first();
        
        return view('sedes.select', compact('empleado'));
    }
}
