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
        return response()->json([
            'message' => 'Examen encontrado',
            'data' => [
                "examen" => []
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
