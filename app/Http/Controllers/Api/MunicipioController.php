<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Municipio;

class MunicipioController extends Controller
{
    public function index()
    {
        $municipios = Municipio::orderBy('nivel', 'desc')->get()->map(function($municipio) {
            return [
                'codigo' => $municipio->id,
                'municipio' => mb_strtolower($municipio->municipio, 'UTF-8'),
                'departamento' => mb_strtolower($municipio->departamento, 'UTF-8')
            ];
        });

        if($municipios->isEmpty()) {
            return response()->json([
                'message' => 'No hay municipios registrados',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Lista de municipios',
            'data' =>$municipios
        ], 200);
    }

    public function show($id)
    {
        $municipio = Municipio::find($id);
        if (!$municipio) {
            return response()->json([
                'message' => 'Municipio no encontrado',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Municipio encontrado',
            'data' =>  $municipio
        ], 200);
    }
    public function buscarMunicipioPorNombre(Request $request)
    {

        $nombre = $request->input('nombre');
        $municipios = Municipio::where('municipio', 'LIKE', '%' . $nombre . '%')->take(5)->get();

        if (!$municipios) {
            return response()->json([
                'message' => 'Municipio no encontrado',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Municipios encontrados',
            'data' =>  $municipios
        ], 200);
    }

    public function departamento($departamento_id)
    {
        $municipios = Municipio::where('codigo_departamento', $departamento_id)->get();
        if ($municipios->isEmpty()) {
            return response()->json([
                'message' => 'No hay municipios registrados para este departamento',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Lista de municipios por departamento',
            'data' => $municipios
        ], 200);

    }

}
