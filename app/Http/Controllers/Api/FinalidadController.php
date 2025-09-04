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
}
