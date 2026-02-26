<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Sede;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;

class SedeController extends Controller
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
    public function show(Sede $sede)
    {

        $examenes = Examen::all();
        
        return view('sedes.show', compact('sede', 'examenes'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sede $sede)
    {
        return view('sedes.edit', compact('sede')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sede $sede)
    {
        //
    }

    /**
     * Choose a Sede for the user.
     */
    public function elegirSede(Sede $sede)
    {
            if (!$sede) {
                return redirect()->back()->with('error', 'Sede no encontrada.');
            }

            session(['sede' => $sede]);
            return redirect()->route('inicio')->with('success', 'Sede seleccionada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sede $sede)
    {
        //
    }
}
