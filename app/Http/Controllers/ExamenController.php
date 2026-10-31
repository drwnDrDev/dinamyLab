<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'busqueda' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F,A',
        ]);
        $busqueda = trim($filters['busqueda'] ?? '');
        $examenes = Examen::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('nombre_alternativo', 'like', "%{$busqueda}%")
                        ->orWhere('cup', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->when($filters['sexo'] ?? null, fn ($query, $sexo) => $query->where('sexo_aplicable', $sexo))
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return view('examenes.index', compact('examenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Examen $examen)
    {

        return view('examenes.show', compact('examen'));
    }

    /**
     * Show batch processing view for exam
     */
    public function lote(Examen $examen)
    {
        return view('examenes.lote', compact('examen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Examen $examen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Examen $examen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Examen $examen)
    {
        //
    }
}
