<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResolucionController extends Controller
{
    public function index()
    {
        $resoluciones = auth()->user()->empleado->sede->resoluciones;
        return view('resoluciones.index', compact('resoluciones'));
    }

    public function create()
    {
        return view('resoluciones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        $data['sede_id'] = auth()->user()->empleado->sede->id;

        Resolucion::create($data);

        return redirect()->route('resoluciones.index')->with('success', 'Resolución creada exitosamente.');
    }

    // Other methods like show, edit, update, destroy can be added here
    public function show(Resolucion $resolucion)
    {
        return view('resoluciones.show', compact('resolucion'));

    }
    public function edit(Resolucion $resolucion)
    {
        return view('resoluciones.edit', compact('resolucion'));
    }

    public function update(Request $request, Resolucion $resolucion)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string',
        ]);

        $resolucion->update($data);

        return redirect()->route('resoluciones.index')->with('success', 'Resolución actualizada exitosamente.');
    }

    public function destroy(Resolucion $resolucion)
    {
        $user = auth()->user();
        if ($user->cannot('delete', $resolucion)) {
            return redirect()->route('resoluciones.index')->withErrors(['error' => 'No tienes permiso para eliminar esta resolución.']);
        }
        $resolucion->delete();

        return redirect()->route('resoluciones.index')->with('success', 'Resolución eliminada exitosamente.');
    }
}
