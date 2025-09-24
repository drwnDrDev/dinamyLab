<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FinalidadController extends Controller
{
    public function index()
    {
        $finalidades = \App\Models\Finalidad::orderBy('nivel','desc')->get();
        if($finalidades->isEmpty()) {
            return response()->json([
                'message' => 'No hay finalidades registradas',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Lista de finalidades',
            'data' => [
                "finalidades" => $finalidades
            ]
        ]);
    }

    public function show($codigo)
    {
        $finalidad = \App\Models\Finalidad::where('codigo', $codigo)->first();

        if (!$finalidad) {
            return response()->json([
                'message' => 'Finalidad no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Finalidad encontrada',
            'data' => $finalidad
        ]);
    }
    public function activar($codigo)
    {
        $finalidad = \App\Models\Finalidad::where('codigo', $codigo)->first();

        if (!$finalidad) {
            return response()->json([
                'message' => 'Finalidad no encontrada',
                'data' => null
            ], 404);
        }

        $finalidad->activo = !$finalidad->activo;
        $finalidad->save();

        return response()->json([
            'message' => 'Finalidad actualizada',
            'data' => $finalidad
        ]);
    }
}
