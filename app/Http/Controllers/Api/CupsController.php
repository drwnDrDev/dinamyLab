<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CodigoCup;

class CupsController extends Controller
{
    public function index()
    {
        $cups = CodigoCup::all();
        if ($cups->isEmpty()) {
            return response()->json(['message' => 'No hay códigos CUP disponibles.'], 404);
        }

        return response()->json(
            ['data' => $cups], 200
        );
    }

    public function show($codigo)
    {
        $cup = CodigoCup::where('codigo', $codigo)->first();
        if (!$cup) {
            return response()->json(['message' => 'Código CUP ' . $codigo . ' no encontrado.'], 404);
        }
        return response()->json(["data"=>$cup], 200);
    }

    public function store(Request $request)
    {
        return CodigoCup::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $cup = CodigoCup::find($id);
        $cup->update($request->all());
        return $cup;
    }

    public function destroy($id)
    {
        return CodigoCup::destroy($id);
    }

    public function buscarPorCodigo($codigo)
    {
        return CodigoCup::where('codigo', $codigo)->get();
    }

    public function buscarPorNombre(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $cups = CodigoCup::query()
            ->where('nombre', 'like', '%' . $validated['nombre'] . '%')
            ->orderBy('nombre')
            ->limit(20)
            ->get(['codigo', 'nombre']);

        return response()->json(['data' => $cups]);
    }

    public function activar($id)
    {
        $cup = CodigoCup::find($id);
        $cup->estado = 'activo';
        $cup->save();
        return $cup;
    }
}
