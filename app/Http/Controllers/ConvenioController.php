<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Models\TipoDocumento;
use App\Policies\EmpresaPolicy;
use App\Services\GuardarContacto;
use Illuminate\Http\Request;

class ConvenioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('convenios.index', [
            'convenios' => Convenio::where('empresa_id', auth()->user()->empleado->empresa_id)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        $documentos = TipoDocumento::where('es_pagador', true)->get();

       return view('convenios.create-react', compact('documentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatos =  $request->validate([
            'razon_social' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:255|unique:convenios',

        ]);

        // Crear el convenio con los datos del contacto
         $convenio =  Convenio::create( [
            'empresa_id' => session('sede')->empresa_id,
            'razon_social' => $request->razon_social,
            'numero_documento' => $request->numero_documento,
            'tipo_documento_id' => TipoDocumento::idPorCodigoDian($request->tipo_documento), // Asumiendo que el código RIPS para NIT es '31'

        ]);
        $contactoDatos = GuardarContacto::guardarContacto($request->all(), $convenio);


        return redirect()->route('convenios.index')->with('success', 'Convenio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Convenio $convenio)
    {

        // Cargar el contacto relacionado con el convenio

        $this->authorize('view', $convenio);
        return view('convenios.show', [
            'convenio' => $convenio,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Convenio $convenio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Convenio $convenio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Convenio $convenio)
    {
        //
    }
}
