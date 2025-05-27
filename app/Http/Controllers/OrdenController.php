<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Municipio;
use App\Models\Examen;
use App\TipoDocumento;
use Illuminate\Http\Request;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::with(['paciente', 'acompaniante'])->groupBy('estado')->get();
        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ciudades = Municipio::all();
        $tipos_documento = collect(TipoDocumento::cases())
            ->mapWithKeys(fn($tipo) => [$tipo->value => $tipo->nombre()]);
        $examenes = Examen::all();
        return view('ordenes.create', compact('tipos_documento', 'ciudades', 'examenes'));
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
    public function show(Orden $orden)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orden $orden)
    {
        return view('ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $orden)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orden $orden)
    {
        can('eliminar_orden_medica', function () use ($orden) {
            $orden->delete();
            return redirect()->route('ordenes')->with('success', 'Orden médica eliminada correctamente');
        });
        return redirect()->route('ordenes')->with('error', 'No tienes permiso para eliminar esta orden médica');
    }
}
