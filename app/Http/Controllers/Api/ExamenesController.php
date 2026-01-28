<?php

namespace App\Http\Controllers\Api;

use App\Estado;
use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Procedimiento;
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
}
