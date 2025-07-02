<?php

namespace App\Http\Controllers;

use App\Models\Sede;
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sede $sede)
    {
        //
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
    public function elgirSede(Request $request)
    {
        $sedeId = $request->input('sede');
        if ($sedeId) {
            $sede = Sede::find($sedeId);
            if (!$sede) {
                return redirect()->back()->with('error', 'Sede no encontrada.');
            }

            session(['sede' => $sede]);
            return redirect()->back()->with('success', 'Sede seleccionada correctamente.');
        }
        return redirect()->back()->with('error', 'Debe seleccionar una sede.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sede $sede)
    {
        //
    }
}
