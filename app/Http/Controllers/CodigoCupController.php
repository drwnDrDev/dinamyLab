<?php

namespace App\Http\Controllers;

use App\Models\CodigoCup;
use Illuminate\Http\Request;

class CodigoCupController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'busqueda' => 'nullable|string|max:100',
            'activo' => 'nullable|in:0,1',
        ]);
        $busqueda = trim($filters['busqueda'] ?? '');
        $codigoCups = CodigoCup::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('grupo', 'like', "%{$busqueda}%");
                });
            })
            ->when(isset($filters['activo']), fn ($query) => $query->where('activo', $request->boolean('activo')))
            ->orderBy('codigo')
            ->paginate(25)
            ->withQueryString();

        return view('cups.index', compact('codigoCups'));
    }

    public function search(Request $request)
    {
        return redirect()->route('cups.index', ['busqueda' => $request->input('search')]);
    }

    public function show(string $codigoCup)
    {
        $codigoCup = CodigoCup::where('codigo', $codigoCup)->firstOrFail();
        return view('cups.show', compact('codigoCup'));
    }
}
