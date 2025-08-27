<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CodigoDiagnostico;

class CodigoDiagnosticoController extends Controller
{
    public function index()
    {
        return CodigoDiagnostico::all();
    }

    public function show($id)
    {
        return CodigoDiagnostico::find($id);
    }

    public function store(Request $request)
    {
        return CodigoDiagnostico::create($request->all());
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

    public function toggleStatus($id)
    {
        
        try {
            $codigoDiagnostico = CodigoDiagnostico::find($id);
            $codigoDiagnostico->activo = !$codigoDiagnostico->activo;
            $codigoDiagnostico->save();


            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }

    public function destroy($id)
    {
        return CodigoDiagnostico::destroy($id);
    }
}
