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
        $sede = session('sede');
        $usuario = auth()->user();
        $empleado = Empleado::where('user_id', $usuario->id)->first();
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
        $pacientesHoy = $procedimientos->count();
        // dd($procedimientos);
        return view('dashboard', compact('empleado', 'procedimientos', 'procedimientosByExamen', 'procedimientosByEstado', 'pacientesHoy', 'ordenes'));
    }

    public function select()
    {
        $usuario = auth()->user();
        $empleado = Empleado::where('user_id', $usuario->id)->first();

        return view('sedes.select', compact('empleado'));
    }
}
