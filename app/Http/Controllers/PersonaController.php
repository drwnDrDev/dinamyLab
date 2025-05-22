<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Persona;
use App\Models\Pais;
use App\Models\Municipio;
use App\TipoDocumento;
use Illuminate\Http\Request;
use App\Http\Requests\StorePersonaRequest;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('personas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paises=Pais::all();
        $ciudades = Municipio::all();
        $tipos_documento = collect(TipoDocumento::cases())
            ->mapWithKeys(fn($tipo) => [$tipo->value => $tipo->nombre()]);
        return view('personas.create', compact('tipos_documento', 'ciudades','paises'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        $request->validated();
        $telefono = $request->input('telefono', null);
        $municipio_id = $request->input('municipio_id', 155);
        $info_adicional =$request->datos_adicionales;
        $contacto = Contacto::create([
            'municipio_id' => $municipio_id,
            'telefono' => $telefono,
            'info_adicional' => json_encode($info_adicional),
        ]);
        // Dividir nombres y apellidos en primer y segundo nombre
        $nombres = explode(' ', trim($request->input('nombres')), 2);
        $apellidos = explode(' ', trim($request->input('apellidos')), 2);

        // asignar booleano a nacional
        $nacional = $request->datos_adicionales['pais'] ==='COL'|| $request->datos_adicionales['pais'] === null;

        // Crear la persona
        $persona = Persona::create( [
            'primer_nombre' => $nombres[0],
            'segundo_nombre' => $nombres[1] ?? NULL,
            'primer_apellido' => $apellidos[0],
            'segundo_apellido' => $apellidos[1] ?? NULL,
            'numero_documento' => $request->input('numero_documento'),
            'tipo_documento' => $request->input('tipo_documento', 'CC'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'sexo' => $request->input('sexo'),
            'nacional' => $nacional,
            'contacto_id' => $contacto->id,
        ]);

        return redirect()->route('personas.show', $persona)->with('success', 'Persona creada correctamente');


    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        return view('personas.show',compact('persona'));
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
