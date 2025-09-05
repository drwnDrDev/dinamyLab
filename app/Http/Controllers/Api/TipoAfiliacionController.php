<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TipoAfiliacionController extends Controller
{
    public function index()
    {
        $tiposAfiliacion = \App\Models\TipoAfiliacion::orderBy('nivel','desc')->get();

        if ($tiposAfiliacion->isEmpty()) {
            return response()->json(['message' => 'No hay tipos de afiliación activos'], 204);
        }

        return response()->json([
            "message" => "Tipos de Afiliación activos",
             "data"=>  $tiposAfiliacion
        ], 200);
    }

    public function show($codigo)
    {
        $tipoAfiliacion = \App\Models\TipoAfiliacion::findOrFail($codigo);
        if (!$tipoAfiliacion) {
            return response()->json(['message' => 'Tipo de afiliación no encontrado'], 404);
        }
        return response()->json(
            [
                "message" => "Tipo de Afiliación encontrado",
                "data" => $tipoAfiliacion
            ], 200
        );
    }
}
