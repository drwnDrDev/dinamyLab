<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoDiagnostico;
use Inertia\Inertia;

class CodigoDiagnosticoController extends Controller
{
    public function index()
    {
        return Inertia::render('Diagnosticos/Index', [
            'diagnosticos' => CodigoDiagnostico::all()
        ]);
    }


    public function show($id)
    {
        return view('diagnosticos.show', ['codigoDiagnostico' => CodigoDiagnostico::find($id)]);
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
        return view('diagnosticos.index', ['codigoDiagnostico' => $codigoDiagnostico]);
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


    public function destroy($id)
    {
        return CodigoDiagnostico::destroy($id);
    }

    
    public function toggleStatus($id)
    {
        return ($id);
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
}
