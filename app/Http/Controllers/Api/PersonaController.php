<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = Persona::all();

        if($personas->isEmpty()) {
            return response()->json([
                'message' => 'No hay personas registradas',
                'data' => []
            ], 404);
        }
       return response()->json([
            'message' => 'Lista de personas',
            'data' => [
                "personas" => $personas
            ]
        ]);
    }

    public function show($id)
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Persona encontrada',
            'data' => [
                "persona" => $persona
            ]
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'numero_documento'=>'required|string|max:20'
        ]);

        // Contacto
        $telefono = $request->input('telefono', null);
        $municipio_id = $request->input('municipio_id', 155);
        $info_adicional =$request->info_adicional;
        $contacto = \App\Models\Contacto::create([
            'municipio_id' => $municipio_id,
            'telefono' => $telefono,
            'info_adicional' => json_encode($info_adicional),
        ]);
        // Dividir nombres y apellidos en primer y segundo nombre
        $nombres = explode(' ', trim($request->input('nombres')), 2);
        $apellidos = explode(' ', trim($request->input('apellidos')), 2);
        // Crear la persona
        $persona = Persona::create( [
            'primer_nombre' => $nombres[0],
            'segundo_nombre' => $nombres[1] ?? '',
            'primer_apellido' => $apellidos[0],
            'segundo_apellido' => $apellidos[1] ?? '',
            'numero_documento' => $request->input('numero_documento'),
            'tipo_documento' => $request->input('tipo_documento', 'CC'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'sexo' => $request->input('sexo'),
            'nacional' => $request->input('nacional', true),
            'contacto_id' => $contacto->id,
        ]);


        return response()->json([
            'message' => 'Persona creada con éxito',
            'data' => [
                "persona" => $persona
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Lógica para actualizar una persona existente
    }

    public function destroy($id)
    {
        // Lógica para eliminar una persona
    }

    public function buscar($numero_documento)
    {

        $persona = Persona::where('numero_documento', $numero_documento)->first();


        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Persona encontrada',
            'persona' => [
                "id" => $persona->id,
                "nombre" => implode(' ',[$persona->primer_nombre,$persona->segundo_nombre?? '']),
                "apellido" => implode(' ',[$persona->primer_apellido,$persona->segundo_apellido?? '']),
                "tipo_documento" => $persona->tipo_documento,
                "numero_documento" => $persona->numero_documento,
                "fecha_nacimiento" => $persona->fecha_nacimiento,
                "sexo" => $persona->sexo,
                "nacional" => $persona->nacional,
                "telefono" => $persona->contacto->telefono ?? 'No disponible',
                "direccion" => $persona->contacto->info_adicional['direccion'] ?? 'No disponible',
                "ciudad" => $persona->contacto->municipio->municipio . ', ' . $persona->contacto->municipio->departamento,

            ]
        ]);
    }
}
