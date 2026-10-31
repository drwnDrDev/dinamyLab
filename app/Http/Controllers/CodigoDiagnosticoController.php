<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoDiagnostico;

class CodigoDiagnosticoController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:100',
            'activo' => 'nullable|in:0,1',
        ]);
        $busqueda = trim($filters['search'] ?? '');
        $codigoDiagnostico = CodigoDiagnostico::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%")
                        ->orWhere('grupo', 'like', "%{$busqueda}%")
                        ->orWhere('sub_grupo', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%");
                });
            })
            ->when(isset($filters['activo']), fn ($query) => $query->where('activo', $request->boolean('activo')))
            ->orderByDesc('nivel')
            ->paginate(25)
            ->withQueryString();
        return view('diagnosticos.index', ['codigoDiagnostico' => $codigoDiagnostico]);
    }

    public function show($id)
    {
        return view('diagnosticos.show', ['codigoDiagnostico' => CodigoDiagnostico::find($id)]);
    }

    public function search(Request $request)
    {
        return redirect()->route('cie10.index', ['search' => $request->input('search')]);
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
