<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaisController extends Controller
{

    public function index()
    {
        $paises = \App\Models\Pais::select('nombre', 'codigo_iso','nivel')->orderBy('nivel', 'desc')->get();
        if($paises->isEmpty()) {
            return response()->json([
                'message' => 'No hay paises registrados',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Lista de paises',
            'data' => [
                "paises" => $paises
            ]
        ]);
    }

}
