<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Services\GuardarContacto;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ConvenioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('convenios.index', [
            'convenios' => Convenio::with('contacto')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    
       return view('convenios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        // Validar los datos del formulario
        $validatos =  $request->validate([
            'razon_social' => 'required|string|max:255',
            'nit' => 'required|string|max:255|unique:convenios,numero_documento',
            'telefono' => 'nullable|string|size:10',
            'direccion' => 'nullable|string|max:255',
            'municipio' => 'nullable|exists:municipios,id',
            'correo' => 'nullable|email|max:255',
            'redes' => 'nullable|array',
            'redes.*' => 'nullable|string|max:255',
        ]);


        // Crear el convenio con los datos del contacto
      $convenio =  Convenio::create( [
            'razon_social' => $request->razon_social,
            'numero_documento' => $request->nit,
            'tipo_documento_id' => 6,

        ]);
        $contactoDatos = GuardarContacto::guardarContacto($validatos, $convenio);
        return redirect()->route('convenios.index')->with('success', 'Convenio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Convenio $convenio)
    {

        // Cargar el contacto relacionado con el convenio
        return view('convenios.show', [
            'convenio' => $convenio->load('contacto'),
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
