<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
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
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'nit' => 'required|string|max:255|unique:convenios,nit',
            'telefono' => 'nullable|string|size:10',
        ]);
        $contactoDatos = $request->only('telefono', 'municipio', 'direccion', 'pais', 'correo','whatsapp','maps','linkin','facebook','instagram','tiktok','youtube','website','otras_redes');

        $contacto = \App\Services\GuardarContacto::guardar($contactoDatos);
        if (!$contacto) {
            Log::warning('contacto sin datos', ['user' => Auth::id()]);
            $contacto = Contactato::find(1);
        }
        // Crear el convenio con los datos del contacto
        Convenio::create( [
            'razon_social' => $request->razon_social,
            'nit' => $request->nit,
            'contacto_id' => $contacto->id,
        ]);

        return redirect()->route('convenios.index')->with('success', 'Convenio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Convenio $convenio)
    {
        //
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
