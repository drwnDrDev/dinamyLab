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

    public function dashboard( )
    {
        $sede = session('sede');
        $usuario = auth()->user();
        $empleado = Empleado::where('user_id', $usuario->id)->first();
        $ordenes = $usuario->hasRole('administrador') ? \App\Models\Orden::all() : \App\Models\Orden::whereHas('procedimientos', function ($query) use ($sede) {
            $query->where('sede_id', $sede->id);
        })->get()   ;
        $procedimientos = $usuario->hasRole('administrador') ? \App\Models\Procedimiento::all() :  \App\Models\Procedimiento::where('sede_id', $sede->id)->get();

        $procedimientosByExamen = $procedimientos->groupBy('examen_id')->map(function ($group) {
            return [
                'examen' => $group->first()->examen->nombre,
                'id' => $group->first()->examen->id,
                'count' => $group->count(),
            ];
        })->sortByDesc('count')->values();


        $procedimientosPendientes = $procedimientos->where('estado', 'en proceso');

        $pacientesHoy = $procedimientos->unique(function ($item) {
            return $item->orden->paciente_id;
        })->count();


        return view('dashboard', compact('procedimientosPendientes', 'empleado', 'procedimientos', 'procedimientosByExamen', 'pacientesHoy', 'ordenes'));
    }

    public function select()
    {
        $usuario = auth()->user();
        $empleado = Empleado::where('user_id', $usuario->id)->first();

        return view('sedes.select', compact('empleado'));
    }
}
