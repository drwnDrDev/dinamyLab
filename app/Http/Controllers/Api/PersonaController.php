<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonaRequest;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Services\GuardarPersona;


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


        $persona = GuardarPersona::guardar($request);
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
        if($request->input('tipo_documento')!==$persona->tipo_documento->cod_rips){
            $persona->tipo_documento_id = \App\Models\TipoDocumento::idPorCodigoRips($request->input('tipo_documento'));
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
                $persona->procedencia()->updateOrCreate(
                    ['procedencia_id' => $persona->id, 'procedencia_type' => Persona::class],
                    ['pais_codigo_iso' => $request->input('pais')]
                );
            } elseif ($request->input('pais')==='COL' && !$persona->nacional) {
                $persona->nacional = true;
                $persona->procedencia()->delete(); // Eliminar la procedencia si es nacional

            }

        }

        // Actualizar los datos de contacto
        if (
            $request->has('telefono') &&
            $request->input('telefono') !== '' &&
            $request->input('telefono') !== null &&
            !$persona->telefonos()->where('numero', $request->input('telefono'))->exists()
        ) {
            $persona->telefonos()->create([
                'numero' => $request->input('telefono'),
            ]);
        } elseif ($request->has('telefono') && $request->input('telefono') === '') {
            $persona->telefonos()->delete(); // Eliminar el teléfono si se envía vacío
        }
        // Actualizar la dirección
            $persona->direccion()->updateOrCreate(
                ['direccionable_id' => $persona->id, 'direccionable_type' => Persona::class],
                [
                    'direccion' => $request->input('direccion', ''),
                    'municipio_id' => $request->input('municipio', 11007) // Valor por defecto para Bogotá
                ]
            );
        // Actualizar el correo electrónico
            // Verificar si el correo electrónico ya existe
            if ($request->has('correo') && $request->input('correo') === '') {
                // Si el correo está vacío, eliminar el correo existente
                $persona->email()->delete();
            } elseif ($request->has('correo') && $request->input('correo')) {
                // Si el correo es diferente, actualizar o crear el correo
                $persona->email()->updateOrCreate(
                    ['emailable_id' => $persona->id, 'emailable_type' => Persona::class],
                    ['email' => $request->input('correo')]
                );
            }
        // Actualizar la afiliación a salud
        if ($request->has('eps') && $request->input('eps') !== '' && $request->input('eps') !== null) {
            $persona->afiliacionSalud()->updateOrCreate(
                ['persona_id' => $persona->id],
                [
                    'eps' => $request->input('eps','Sin EPS'),
                    'tipo_afiliacion' => $request->input('tipo_afiliacion', 'subsidiado')
                ]
            );
        }
        // Actualizar el contacto de emergencia
        if ($request->has('contacto_emergencia')) {
            $contactoData = $request->input('contacto_emergencia');
            $contacto = $persona->contactoEmergencia()->updateOrCreate(
                ['paciente_id' => $persona->id],
                [
                    'nombre' => $contactoData['nombre'] ?? '',
                    'telefono' => $contactoData['telefono'] ?? '',
                    'parentesco' => $contactoData['parentesco'] ?? '',
                ]
            );
        }



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
                "tipo_documento" => $persona->tipo_documento->cod_rips,
                "numero_documento" => $persona->numero_documento,
                "fecha_nacimiento" => $persona->fecha_nacimiento? $persona->fecha_nacimiento->format('Y-m-d') : null,
                "sexo" => $persona->sexo,
                "nacional" => $persona->nacional,
                "telefono" => $persona->telefonos?->first()->numero ?? null,
                "direccion" => $persona->direccion?->direccion ?? null,
                "correo" => $persona->email?->email ?? null,
                "pais" => $persona->procedencia?->pais_codigo_iso ?? 'COL',
                "municipio" => $persona->direccion?->municipio_id ?? 11007,
                'eps' => $persona->afiliacionSalud?->eps ?? null,

            ]
        ]);
    }
}
