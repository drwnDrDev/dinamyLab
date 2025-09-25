<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ModalidadAtencionController extends Controller
{
    public function index()
    {
        $modalidades = \App\Models\ModalidadAtencion::orderBy('nivel','desc')->get();
        if($modalidades->isEmpty()) {
            return response()->json([
                'message' => 'No hay modalidades de atencion registradas',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Lista de modalidades de atencion',
            'data' => [
                "modalidades_atencion" => $modalidades
            ]
        ]);
    }
    public function show($codigo)
    {
        $modalidad = \App\Models\ModalidadAtencion::where('codigo', $codigo)->first();

        if (!$modalidad) {
            return response()->json([
                'message' => 'Modalidad de atencion no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Modalidad de atencion encontrada',
            'data' => $modalidad
        ]);
    }
    public function activar($codigo)
    {
        $modalidad = \App\Models\ModalidadAtencion::where('codigo', $codigo)->first();
        if (!$modalidad) {
            return response()->json([
                'message' => 'Modalidad de atencion no encontrada',
                'data' => null
            ], 404);
        }
        $modalidad->activo = !$modalidad->activo;
        $modalidad->save();
        return response()->json([
            'message' => 'Modalidad de atencion actualizada',
            'data' => $modalidad
        ]);
    }
}
