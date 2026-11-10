<?php

namespace App\Http\Controllers;


use App\Models\Persona;
use App\Models\Pais;
use App\Models\Municipio;
use App\TipoDocumento;
use Illuminate\Http\Request;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Models\Eps;
use App\Models\Procedimiento;
use App\Services\GuardarPersona;
use App\Services\ControlSede;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sexo = $request->input('sexo');

        if (is_string($sexo)) {
            $request->merge(['sexo' => strtoupper(trim($sexo))]);
        }

        $filters = $request->validate([
            'busqueda' => 'nullable|string|max:100',
            'sexo' => 'nullable|exists:sexos,codigo',
            'municipio_id' => 'nullable|integer|exists:municipios,id',
            'eps' => 'nullable|string|max:255',
        ]);
        $busqueda = trim($filters['busqueda'] ?? '');
        $query = Persona::query()->with(['direccion.municipio', 'afiliacionSalud']);

        if ($busqueda !== '') {
            $query->where(function ($query) use ($busqueda) {
                $query->where('numero_documento', 'like', "%{$busqueda}%")
                    ->orWhere('primer_nombre', 'like', "%{$busqueda}%")
                    ->orWhere('segundo_nombre', 'like', "%{$busqueda}%")
                    ->orWhere('primer_apellido', 'like', "%{$busqueda}%")
                    ->orWhere('segundo_apellido', 'like', "%{$busqueda}%");
            });
        }

        $query->when($filters['sexo'] ?? null, fn ($query, $sexo) => $query->where('sexo', $sexo));
        $query->when($filters['municipio_id'] ?? null, fn ($query, $municipioId) => $query->whereHas(
            'direccion',
            fn ($direccion) => $direccion->where('municipio_id', $municipioId)
        ));
        $query->when($filters['eps'] ?? null, fn ($query, $eps) => $query->whereHas(
            'afiliacionSalud',
            fn ($afiliacion) => $afiliacion->where('eps', $eps)
        ));

        $personas = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $municipios = Municipio::query()->orderBy('municipio')->get(['id', 'municipio']);
        $eps = \App\Models\AfiliacionSalud::query()
            ->whereNotNull('eps')
            ->where('eps', '!=', '')
            ->distinct()
            ->orderBy('eps')
            ->pluck('eps');

        return view('personas.index', compact('personas', 'municipios', 'eps'));
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
        $sede = request()->user()->hasRole('admin')
            ? null
            : ControlSede::autorizarSedeActiva(request());

        $persona->load(['direccion.municipio', 'telefonos', 'email', 'redesSociales', 'afiliacionSalud','ordenes']);
        $ordenesIds = $persona->ordenes()->pluck('id');

        if ($ordenesIds->isEmpty()) {
            $procedimientos = collect(); // Colección vacía
        } else {
            $procedimientos = Procedimiento::when($sede, fn ($query) => $query->where('sede_id', $sede->id))
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
        return view('personas.edidt', compact('persona'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        $validated = $request->validated();

        $persona->update($validated);

        if ($request->filled('telefono')) {
            $persona->telefonos()->updateOrCreate(
                ['numero' => $validated['telefono']],
                ['numero' => $validated['telefono']]
            );
        }

        return redirect()->route('personas.show', $persona)->with('success', 'Paciente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('personas')->with('success', 'Paciente eliminado correctamente.');
    }
}
