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
        $paises=Pais::all();
        $ciudades = Municipio::all()->sortByDesc('nivel');
        $eps =Eps::all()->sortBy('nombre');
        $tipos_documento = collect(TipoDocumento::cases())
            ->mapWithKeys(fn($tipo) => [$tipo->value => $tipo->nombre()]);
        return view('personas.create', compact('tipos_documento', 'ciudades','paises', 'eps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {

        $persona = GuardarPersona::guardar($request->validated());
        return redirect()->route('personas.show', $persona)->with('success', 'Persona creada correctamente');


    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        $sede = session('sede');
        if (!$sede) {
            return redirect()->route('home')->with('error', 'No se ha seleccionado una sede');
        }

        $persona->load(['direccion.municipio', 'telefonos', 'email', 'redesSociales', 'afiliacionSalud']);
        $procedimientos = Procedimiento::where('sede_id',$sede)
        ->get()
        ->load(['orden','examen'])->when($persona->ordenes->isNotEmpty(), function ($query) use ($persona) {
            return $query->whereIn('orden_id', $persona->ordenes->pluck('id'));
        })->groupBy('estado');


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
