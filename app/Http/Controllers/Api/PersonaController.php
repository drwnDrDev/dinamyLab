<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonaRequest;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Services\NombreParser;

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
            'data' =>  $personas

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
            'data' => $persona

        ]);
    }

    public function store(StorePersonaRequest $request)
    {

        $request->validated();
        $contactoDatos = $request->only('telefono', 'municipio', 'eps', 'direccion', 'pais', 'correo');
  
        $contacto = \App\Services\GuardarContacto::guardar($contactoDatos);
        if (!$contacto) {
            Log::warning('contacto sin datos', ['user' => Auth::id()]);
            $contacto = Contactato::find(1);
        }

        // Dividir nombres y apellidos en primer y segundo nombre
        $parsed = NombreParser::parsearPersona(
            $request->input('nombres'),
            $request->input('apellidos')
        );
        // Crear la persona
        $persona = Persona::create( [
            'primer_nombre' => $parsed['primer_nombre'],
            'segundo_nombre'=> $parsed['segundo_nombre'],
            'primer_apellido' => $parsed['primer_apellido'],
            'segundo_apellido' => $parsed['segundo_apellido'],
            'numero_documento' => $request->input('numero_documento'),
            'tipo_documento' => $request->input('tipo_documento', 'CC'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'sexo' => $request->input('sexo'),
            'nacional' => $request->input('nacional', true),
            'contacto_id' => $contacto->id,
        ]);


        return response()->json([
            'message' => 'Persona creada con éxito',
            'data' => $persona

        ], 201);
    }

    public function update(Request $request, $id)
    {

        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada',
                'data' => null
            ], 404);
        }
        if ($persona->numero_documento !== $request->input('numero_documento')) {
            return response()->json([
                'message' => 'El número de documento no puede ser modificado',
                'data' => null
            ], 400);
        }


        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'tipo_documento' => 'required|string|max:2',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|string|max:10',
            'telefono' => 'nullable|string|size:10',
            'municipio' => 'required|exists:municipios,id',
        ]);

        // Actualizar los campos de la persona
        if($request->input('nombres')!==$persona->nombres()){
             $nombres = explode(' ', trim($request->input('nombres')), 2);
                $persona->primer_nombre = $nombres[0];
                $persona->segundo_nombre = $nombres[1] ?? '';
        }
        if($request->input('apellidos')!==$persona->apellidos()){
            $apellidos = explode(' ', trim($request->input('apellidos')), 2);
            $persona->primer_apellido = $apellidos[0];
            $persona->segundo_apellido = $apellidos[1] ?? '';
        }
        if($request->input('tipo_documento')!==$persona->tipo_documento){
            $persona->tipo_documento = $request->input('tipo_documento', 'CC');
        }
        if($request->input('fecha_nacimiento')!==$persona->fecha_nacimiento){
            $persona->fecha_nacimiento = $request->input('fecha_nacimiento');
        }
        if($request->input('sexo')!==$persona->sexo){
            $persona->sexo = $request->input('sexo');
        }
        if($request->has('pais')){
            if($request->input('pais')!=='COL'&& $request->input('pais')!=='' && $persona->nacional){
                $persona->nacional = false;
                $persona->contacto->info_adicional = json_encode(
                    array_merge(
                        json_decode($persona->contacto->info_adicional, true),
                        ['pais' => $request->input('pais')]
                    )
                );
            } elseif ($request->input('pais')==='COL' && !$persona->nacional) {
                $persona->nacional = true;
                $info_adicional = json_decode($persona->contacto->info_adicional, true);
                unset($info_adicional['pais']);
                $persona->contacto->info_adicional = json_encode($info_adicional);

            }

        }


            $persona->contacto->telefono = $request->input('telefono', null);
            $persona->contacto->municipio_id = $request->input('municipio', 11007); // Valor por defecto para Bogotá

            $info_adicional = array_filter(
                $request->only('eps','direccion','pais','correo') ?? [],
                function ($value) {
                    return !is_null($value);
                }
            );
            $persona->contacto->info_adicional = json_encode($info_adicional);
            $persona->contacto->save();


        // Guardar los cambios en la persona

        $persona->save();
        return response()->json([
            'message' => 'Persona actualizada con éxito',
            'data' => $persona
        ]);
    }
    public function destroy($id)
    {
        // Lógica para eliminar una persona
    }

    public function buscar($numero_documento)
    {

        $persona = Persona::where('numero_documento', $numero_documento)->first();

        if (!$persona) {
            return response()->json([], 204 );
        }

        return response()->json([
            'message' => 'Persona encontrada',
            'data' => [
                "id" => $persona->id,
                "nombre" => implode(' ',[$persona->primer_nombre,$persona->segundo_nombre?? '']),
                "apellido" => implode(' ',[$persona->primer_apellido,$persona->segundo_apellido?? '']),
                "tipo_documento" => $persona->tipo_documento,
                "numero_documento" => $persona->numero_documento,
                "fecha_nacimiento" => $persona->fecha_nacimiento? $persona->fecha_nacimiento->format('Y-m-d') : null,
                "sexo" => $persona->sexo,
                "nacional" => $persona->nacional,
                "telefono" => $persona->contacto->telefono,
                "direccion" => $persona->contacto->infoAdicional('direccion'),
                "correo" => $persona->contacto->infoAdicional('correo'),
                "pais" => $persona->contacto->infoAdicional('pais'),
                "municipio" => $persona->contacto->municipio->id,
                "ciudad" => $persona->contacto->municipio->municipio,
                'eps' => $persona->contacto->infoAdicional('eps'),

            ]
        ]);
    }
}
