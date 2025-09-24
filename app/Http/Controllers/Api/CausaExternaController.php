<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CausaExternaController extends Controller
{
    public function index()
    {
        $causas = \App\Models\CausaExterna::orderBy('nivel','desc')->get();
        if($causas->isEmpty()) {
            return response()->json([
                'message' => 'No hay causas externas registradas',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Lista de causas externas',
            'data' => [
                "causas_externas" => $causas
            ]
        ]);
    }
    public function show($codigo)
    {
        $causa = \App\Models\CausaExterna::where('codigo', $codigo)->first();

        if (!$causa) {
            return response()->json([
                'message' => 'Causa externa no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Causa externa encontrada',
            'data' => $causa
        ]);
    }
    public function activar($codigo)
    {
        $causa = \App\Models\CausaExterna::where('codigo', $codigo)->first();

        if (!$causa) {
            return response()->json([
                'message' => 'Causa externa no encontrada',
                'data' => null
            ], 404);
        }

        $causa->activo = !$causa->activo;
        $causa->save();

        return response()->json([
            'message' => 'Causa externa actualizada',
            'data' => $causa
        ]);
    }
}
