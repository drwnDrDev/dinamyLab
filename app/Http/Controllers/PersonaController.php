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
    public function index(Request $request)
    {
        $filters = $request->validate([
            'busqueda' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F,I',
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
