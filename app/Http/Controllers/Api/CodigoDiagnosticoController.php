<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CodigoDiagnostico;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\json;

class CodigoDiagnosticoController extends Controller
{
    public function index()
    {
        $codigoDiagnostico = CodigoDiagnostico::all();
        if($codigoDiagnostico->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron códigos de diagnóstico',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Códigos de diagnóstico encontrados',
            'data' => [
                "codigoDiagnostico" => $codigoDiagnostico
            ]
        ], 200);
    }

    public function show($id)
    {
        $codigoDiagnostico = CodigoDiagnostico::find($id);
        if (!$codigoDiagnostico) {
            return response()->json([
                'message' => 'Código de diagnóstico no encontrado',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Código de diagnóstico encontrado',
            'data' => [
                "codigoDiagnostico" => $codigoDiagnostico
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        Auth::can('crear_codigo_diagnostico');
        $request->validate([
            'codigo' => 'required|string|max:10|unique:codigo_diagnosticos,codigo',
            'nombre' => 'required|string|max:255',
            'activo' => 'boolean',
        ]);
        $codigoDiagnostico = new CodigoDiagnostico($request->all());
        $codigoDiagnostico->save();

        return response()->json([
            'message' => 'Código de diagnóstico creado exitosamente',
            'data' => [
                "codigoDiagnostico" => $codigoDiagnostico
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $codigoDiagnostico = CodigoDiagnostico::find($id);
        $codigoDiagnostico->update($request->all());
        return $codigoDiagnostico;
    }

        public function activar($id)
    {
        $codigoDiagnostico = CodigoDiagnostico::find($id);
        $codigoDiagnostico->activo = !$codigoDiagnostico->activo;
        $codigoDiagnostico->save();
        return response()->json([
            'message' => 'Estado actualizado',
            'data' => [
                "codigoDiagnostico" => $codigoDiagnostico
            ]
        ]);
    }

    public function destroy($id)
    {
        return CodigoDiagnostico::destroy($id);
    }
     public function search(Request $request)
    {
        $query = $request->input('search');
        $codigoDiagnostico = CodigoDiagnostico::where('nombre', 'like', "%$query%")
        ->orWhere('descripcion', 'like', "%$query%")
        ->orWhere('grupo', 'like', "%$query%")
        ->orWhere('sub_grupo', 'like', "%$query%")
        ->orWhere('codigo', 'like', "%$query%")
        ->orderBy('nivel', 'DESC')
        ->paginate(10);
        return response()->json(
            [
                'message' => 'Resultados de búsqueda',
                'data' => [
                    "codigoDiagnostico" => $codigoDiagnostico
                ]
            ]
        );
    }
}
