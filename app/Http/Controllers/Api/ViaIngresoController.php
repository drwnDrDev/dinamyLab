<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ViaIngresoController extends Controller
{
    public function index()
    {
        $viasIngreso = \App\Models\ViaIngreso::orderBy('nivel','desc')->get();

        if($viasIngreso->isEmpty()) {
            return response()->json([
                'message' => 'No hay vías de ingreso registradas',
                'data' => []
            ], 404);
        }

       return response()->json([
            'message' => 'Lista de vías de ingreso',
            'data' => $viasIngreso
        ]);
    }
    public function show($codigo)
    {
        $viaIngreso = \App\Models\ViaIngreso::where('codigo', $codigo)->first();

        if (!$viaIngreso) {
            return response()->json([
                'message' => 'Vía de ingreso no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Vía de ingreso encontrada',
            'data' => $viaIngreso
        ]);
    }
    public function activar($codigo)
    {
        $viaIngreso = \App\Models\ViaIngreso::where('codigo', $codigo)->first();

        if (!$viaIngreso) {
            return response()->json([
                'message' => 'Vía de ingreso no encontrada',
                'data' => null
            ], 404);
        }

        $viaIngreso->activo = !$viaIngreso->activo;
        $viaIngreso->save();

        return response()->json([
            'message' => 'Vía de ingreso actualizada',
            'data' => $viaIngreso
        ]);
    }
}
