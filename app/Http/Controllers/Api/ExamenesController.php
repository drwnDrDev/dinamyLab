<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use Illuminate\Http\Request;

class ExamenesController extends Controller
{
    public function index()
    {
        $examenes = Examen::all();
        return response()->json([
            'message' => 'Lista de examenes',
            'data' => [
                "examenes" => $examenes
            ]
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
}
