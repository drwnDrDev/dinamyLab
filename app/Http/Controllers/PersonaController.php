<?php

namespace App\Http\Controllers;


use App\Models\Persona;
use App\Models\Pais;
use App\Models\Municipio;
use App\TipoDocumento;
use Illuminate\Http\Request;
use App\Http\Requests\StorePersonaRequest;
use App\Models\Eps;
use App\Models\Procedimiento;
use App\Services\GuardarPersona;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personas = Persona::all();
        return view('personas.index', compact('personas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('personas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        $validated = $request->validated();
      
        $persona = GuardarPersona::guardar($request->all());
        return redirect()->route('personas.show', $persona)->with('success', 'Persona creada correctamente');


    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        $sede = session('sede');
        if (!$sede) {
            return to_route('dashboard')->with('error', 'No se ha seleccionado una sede');
        }

        $persona->load(['direccion.municipio', 'telefonos', 'email', 'redesSociales', 'afiliacionSalud','ordenes']);
        $ordenesIds = $persona->ordenes()->pluck('id');

        if ($ordenesIds->isEmpty()) {
            $procedimientos = collect(); // Colección vacía
        } else {
            $procedimientos = Procedimiento::where('sede_id', $sede->id)
                ->whereIn('orden_id', $ordenesIds)
                ->with(['orden', 'examen'])
                ->get()
                ->groupBy('estado');
        }



        return view('personas.show',compact('persona','procedimientos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persona $persona)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Persona $persona)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        //
    }
}
