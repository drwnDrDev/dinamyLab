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

     
        if(!$usuario->hasRole('admin') && !$usuario->hasRole('prestador') && !$usuario->hasRole('coordinador') && !$empleado) {
            return view('paciente.dashboard');
        }
        if ($usuario->hasRole('admin')) {

            $pendientes = \App\Models\Procedimiento::where('estado', 'pendiente')->get();

            return view('admin.dashboard',compact('pendientes'));
        }
        return view('dashboard',compact('empleado'));
    }
}
