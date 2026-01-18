<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Services\ParseadorListaPersonas;
use Illuminate\Http\Request;

class ListaPersonasController extends Controller
{
    /**
     * Parsea una lista de personas y enriquece con datos existentes
     * POST /api/personas/parsear-lista
     *
     * Body:
     * {
     *   "contenido": "Carlos Ramirez,1012555321\nLuiz Alberto Diaz, 10101010\nZonia Ramirez Fierro,\nLiliana Diaz Marun, 123123654",
     *   "tipo_documento": "CC" (opcional)
     * }
     */
    public function parsearLista(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
            'tipo_documento' => 'nullable|string|max:5',
        ]);

        $contenido = $request->input('contenido');
        $tipoDocumento = $request->input('tipo_documento', 'CC');

        try {
            // Parsear la lista
            $personasParseadas = ParseadorListaPersonas::parsear($contenido, $tipoDocumento);

            // Enriquecer con datos existentes
            $personasEnriquecidas = $this->enriquecerPersonas($personasParseadas);

            return response()->json([
                'message' => 'Lista parseada correctamente',
                'data' => $personasEnriquecidas,
                'total' => count($personasEnriquecidas),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al parsear la lista',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Enriquece las personas parseadas con datos de la BD
     * Si existe una persona con el mismo número de documento, trae todos sus datos
     */
    private function enriquecerPersonas(array $personasParseadas): array
    {
        $enriquecidas = [];

        foreach ($personasParseadas as $persona) {
            if (!empty($persona['numero_documento'])) {
                // Buscar en la BD
                $personaExistente = Persona::where('numero_documento', $persona['numero_documento'])
                    ->first();

                if ($personaExistente) {
                    $enriquecidas[] = [
                        'id' => $personaExistente->id,
                        'tipo_documento' => $personaExistente->tipo_documento->cod_rips ?? 'CC',
                        'numero_documento' => $personaExistente->numero_documento,
                        'primer_nombre' => $personaExistente->primer_nombre,
                        'segundo_nombre' => $personaExistente->segundo_nombre ?? '',
                        'primer_apellido' => $personaExistente->primer_apellido,
                        'segundo_apellido' => $personaExistente->segundo_apellido ?? '',
                        'fecha_nacimiento' => $personaExistente->fecha_nacimiento,
                        'sexo' => $personaExistente->sexo,
                        'pais_origen' => $personaExistente->pais_origen ?? '170',
                        'telefono' => $personaExistente->telefono ?? '',
                        'zona' => $personaExistente->zona ?? '02',
                        'pais_residencia' => $personaExistente->pais_residencia ?? '170',
                        'correo' => '',
                        'eps' => '',
                        'tipo_afiliacion' => '',
                        'existente' => true,
                        'nombres_completos' => $persona['nombres_completos'],
                    ];
                    continue;
                }
            }

            // Si no existe, retornar los datos parseados
            $enriquecidas[] = array_merge($persona, [
                'id' => null,
                'fecha_nacimiento' => '',
                'sexo' => '',
                'pais_origen' => '170',
                'zona' => '02',
                'pais_residencia' => '170',
                'correo' => '',
                'eps' => '',
                'tipo_afiliacion' => '',
            ]);
        }

        return $enriquecidas;
    }
}
