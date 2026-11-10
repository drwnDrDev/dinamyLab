<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiUpdatePersonaRequest;
use App\Http\Requests\StorePersonaRequest;
use App\Models\Persona;
use App\Models\TipoDocumento;
use App\Services\GuardarPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function index()
    {


        $personas = Persona::get('*')->load('tipo_documento');


        if($personas->isEmpty()) {
            return response()->json([
                'message' => 'No hay personas registradas',
                'data' => []
            ], 404);
        }

       return response()->json([
            'message' => 'Lista de personas',
            'data' => array_map(function ($persona) {
                return [
                    "id" => $persona['id'],
                    "nombre" => implode(' ',[$persona['primer_nombre'],$persona['segundo_nombre']?? '']),
                    "apellido" => implode(' ',[$persona['primer_apellido'],$persona['segundo_apellido']?? '']),
                    "sexo" => $persona['sexo'],
                    "fecha_nacimiento" => $persona['fecha_nacimiento'] ?? null,
                    "tipo_documento" => $persona['tipo_documento']['cod_rips'],
                    "numero_documento" => $persona['numero_documento'],

                ];
            }, $personas->toArray())

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

        $validated = $request->validated();

        $persona = GuardarPersona::guardar($request->all());
        if (!$persona) {
            return response()->json([
                'message' => 'Error al crear la persona',
                'data' => null
            ], 500);
        }
        return response()->json([
            'message' => 'Persona creada con éxito',
            'data' => $persona

        ], 201);
    }

    public function update(ApiUpdatePersonaRequest $request, $id)
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return response()->json([
                'message' => 'Persona no encontrada',
                'data' => null
            ], 404);
        }
        $validated = $request->validated();

        if (
            array_key_exists('numero_documento', $validated)
            && $persona->numero_documento !== $validated['numero_documento']
        ) {
            return response()->json([
                'message' => 'El número de documento no puede ser modificado',
                'data' => null
            ], 400);
        }

        DB::transaction(function () use ($persona, $validated): void {
            $this->updatePersonaProperties($persona, $validated);
            $this->updateContactProperties($persona, $validated);
        });

        $persona->load([
            'tipo_documento',
            'telefonos',
            'direccion',
            'email',
            'afiliacionSalud',
            'contactoEmergencia.acompanante',
        ]);

        return response()->json([
            'message' => 'Persona actualizada con éxito',
            'data' => $persona
        ]);
    }

    private function updatePersonaProperties(Persona $persona, array $data): void
    {
        $attributes = [];

        if (array_key_exists('nombres', $data)) {
            $parts = preg_split('/\s+/', trim($data['nombres']), 2);
            $attributes['primer_nombre'] = $parts[0];
            $attributes['segundo_nombre'] = $parts[1] ?? null;
        }

        if (array_key_exists('apellidos', $data)) {
            $parts = preg_split('/\s+/', trim($data['apellidos']), 2);
            $attributes['primer_apellido'] = $parts[0];
            $attributes['segundo_apellido'] = $parts[1] ?? null;
        }

        foreach ([
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'fecha_nacimiento',
            'sexo',
        ] as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        if (array_key_exists('tipo_documento', $data)) {
            $attributes['tipo_documento_id'] = TipoDocumento::idPorCodigoRips($data['tipo_documento']);
        }

        if (array_key_exists('pais_origen', $data) || array_key_exists('pais', $data)) {
            $attributes['pais_origen'] = $data['pais_origen'] ?? $data['pais'];
        }

        if ($attributes !== []) {
            $persona->update($attributes);
        }
    }

    private function updateContactProperties(Persona $persona, array $data): void
    {
        if (array_key_exists('telefono', $data)) {
            $telefono = $data['telefono'];
            if ($telefono === null || $telefono === '') {
                $persona->telefonos()->delete();
            } else {
                $actual = $persona->telefonos()->first();
                if ($actual) {
                    $actual->update(['numero' => $telefono]);
                } else {
                    $persona->telefonos()->create(['numero' => $telefono]);
                }
            }
        }

        $direccionFields = [
            'direccion' => 'direccion',
            'municipio' => 'municipio_id',
            'pais_residencia' => 'pais_id',
            'codigo_postal' => 'codigo_postal',
            'zona' => 'zona',
        ];
        $direccion = [];
        foreach ($direccionFields as $input => $column) {
            if (array_key_exists($input, $data)) {
                $direccion[$column] = $data[$input];
            }
        }
        if ($direccion !== []) {
            $persona->direccion()->updateOrCreate([], $direccion);
        }

        if (array_key_exists('correo', $data)) {
            if ($data['correo'] === null || $data['correo'] === '') {
                $persona->email()->delete();
            } else {
                $persona->email()->updateOrCreate([], ['email' => $data['correo']]);
            }
        }

        if (array_key_exists('eps', $data) || array_key_exists('tipo_afiliacion', $data)) {
            $afiliacion = $persona->afiliacionSalud()->first();
            if (array_key_exists('eps', $data) && ($data['eps'] === null || $data['eps'] === '')) {
                $persona->afiliacionSalud()->delete();
            } elseif ($afiliacion) {
                $afiliacion->update(array_intersect_key($data, array_flip(['eps', 'tipo_afiliacion'])));
            } elseif (array_key_exists('eps', $data)) {
                $persona->afiliacionSalud()->create([
                    'eps' => $data['eps'],
                    'tipo_afiliacion' => $data['tipo_afiliacion'] ?? '12',
                ]);
            }
        }

        if (array_key_exists('contacto_emergencia', $data)) {
            $contactoData = $data['contacto_emergencia'];
            if ($contactoData === null) {
                $persona->contactoEmergencia()->delete();
                return;
            }

            $contacto = $persona->contactoEmergencia()->first();
            if ($contacto) {
                $contacto->update($contactoData);
            } else {
                $persona->contactoEmergencia()->create($contactoData);
            }
        }
    }
    public function destroy($id)
    {
        $persona = Persona::find($id);

        if (! $persona) {
            return response()->json([
                'message' => 'Persona no encontrada',
                'data' => null,
            ], 404);
        }

        $persona->delete();

        return response()->json([
            'message' => 'Persona eliminada correctamente',
        ], 200);
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
                "pais_origen" => $persona->pais_origen ?? 170,
                "telefono" => $persona->telefonos?->first()->numero ?? null,
                "direccion" => $persona->direccion?->direccion ?? null,
                "codigo_postal" => $persona->direccion?->codigo_postal ?? null,
                "pais_residencia" => $persona->direccion?->pais_id ?? '170',
                "correo" => $persona->email?->email ?? null,
                "pais_origen" => $persona->pais_origen ?? null,
                "municipio" => $persona->direccion?->municipio_id ?? 11001,
                "zona" => $persona->direccion?->zona ?? '02',
                'eps' => $persona->afiliacionSalud?->eps ?? null,
                'tipo_afiliacion' => $persona->afiliacionSalud?->tipo_afiliacion ?? null,

            ]
        ]);
    }

    public function cambiarNombre(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
        ]);

        $persona->update([
            'primer_nombre' => $request->input('primer_nombre'),
            'segundo_nombre' => $request->input('segundo_nombre'),
            'primer_apellido' => $request->input('primer_apellido'),
            'segundo_apellido' => $request->input('segundo_apellido'),
        ]);

        return response()->json([
            'message' => 'Nombre actualizado con éxito',
            'data' => $persona
        ]);
    }
}
