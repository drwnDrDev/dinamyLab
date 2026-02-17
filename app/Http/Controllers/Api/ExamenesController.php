<?php

namespace App\Http\Controllers\Api;

use App\Estado;
use App\Http\Controllers\Controller;
use App\Models\Convenio;
use App\Models\Examen;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Services\EscogerPrecioExamen;
use Illuminate\Http\Request;

class ExamenesController extends Controller
{
    public function index()
    {
        $examenes = Examen::select('cup','id','nombre','valor','nombre_alternativo','sexo_aplicable')->orderBy('nivel','desc')->get();

        return response()->json([
            'message' => 'Lista de examenes',
            'data' => $examenes
        ]);
    }

    public function show($id)
    {
        $examen = Examen::find($id);
        if (!$examen) {
            return response()->json([
                'message' => 'Examen no encontrado',
                'data' => null
            ], 404);
        }
        return response()->json([
            'message' => 'Examen encontrado',
            'data' => [
                "examen" => $examen
            ]
        ]);
    }

    public function store(Request $request)
    {

        return response()->json([
            'message' => 'Examen creado',
            'data' => [
                "examen" => []
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $examen = Examen::find($id);
        if (!$examen) {
            return response()->json([
                'message' => 'Examen no encontrado',
                'data' => null
            ], 404);
        }
    $examen->update($request->except('_token', '_method','submit'));
        return response()->json([
            'message' => 'Examen actualizado',
            'success' => true,
            'data' => [
                "examen" => $examen
            ]
        ]);
    }


    /**
     * Obtener procedimientos pendientes para un examen específico
     */
    public function obtenerProcedimientosPendientes($examenId)
    {
        try {
            $procedimientos = Procedimiento::where('examen_id', $examenId)
                ->where('estado', Estado::PROCESO)
                ->with([
                    'orden.paciente',
                    'examen'
                ])
                ->get()
                ->map(function ($proc) {
                    return [
                        'id' => $proc->id,
                        'orden_id' => $proc->orden_id,
                        'paciente_nombre' => $proc->orden->paciente->nombreCompleto() ?? 'N/A',
                        'paciente_documento' => $proc->orden->paciente->numero_documento ?? 'N/A',
                        'fecha' => $proc->fecha ? $proc->fecha->format('Y-m-d H:i') : 'N/A',
                        'estado' => $proc->estado,
                        'enviar' => true, // Por defecto, marcar para enviar
                        'procedimiento' => $proc
                    ];
                });

            return response()->json([
                'message' => 'Procedimientos pendientes obtenidos',
                'procedimientos' => $procedimientos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener procedimientos: ' . $e->getMessage(),
                'procedimientos' => []
            ], 500);
        }
    }

    public function updateValor(Request $request, string $id)
    {
        $examen = Examen::findOrFail($id);
        $examen->valor = $request->input('valor');
        $examen->save();

        return response()->json([
            'message' => 'Examen actualizado',
            'data' => 201
        ]);
    }

    /**
     * Obtiene el precio inteligente de un examen según convenio y sede.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerPrecio(Request $request)
    {
        // Validar parámetros
        $validated = $request->validate([
            'examen_id' => 'required|integer|exists:examenes,id',
            'convenio_id' => 'required|integer|exists:convenios,id',
            'sede_id' => 'required|integer|exists:sedes,id',
        ]);

        try {
            // Cargar modelos
            $examen = Examen::findOrFail($validated['examen_id']);
            $convenio = Convenio::findOrFail($validated['convenio_id']);
            $sede = Sede::findOrFail($validated['sede_id']);

            // Obtener precio inteligente
            $resultado = EscogerPrecioExamen::obtener($examen, $convenio, $sede);

            return response()->json([
                'message' => 'Precio obtenido exitosamente',
                'data' => [
                    'examen_id' => $examen->id,
                    'examen_nombre' => $examen->nombre,
                    'valor_base' => (float) $examen->valor,
                    'precio_final' => $resultado['precio'],
                    'tipo_tarifa' => $resultado['tipo_tarifa'],
                    'tarifa_id' => $resultado['tarifa_id'],
                    'convenio_id' => $convenio->id,
                    'convenio_nombre' => $convenio->razon_social,
                    'sede_id' => $sede->id,
                    'sede_nombre' => $sede->nombre,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Modelo no encontrado',
                'error' => $e->getMessage()
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el precio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
