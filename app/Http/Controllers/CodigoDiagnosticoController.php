<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoDiagnostico;

class CodigoDiagnosticoController extends Controller
{
    public function index()
    {
        $codigoDiagnostico = CodigoDiagnostico::orderBy('nivel', 'DESC')->paginate(10);
        return view('diagnosticos.index', ['codigoDiagnostico' => $codigoDiagnostico]);
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
        $request->validate([
            'codigo' => 'required|string|max:10',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'nivel' => 'required|integer|min:1|max:5',
            'grupo' => 'nullable|string|max:100',
            'sub_grupo' => 'nullable|string|max:100',
            'activo' => 'required|boolean',
        ]);
        $codigoDiagnostico = CodigoDiagnostico::find($id);
        $codigoDiagnostico->update($request->all());
        return $codigoDiagnostico;
    }


    public function destroy($id)
    {
        return CodigoDiagnostico::destroy($id);
    }
}
