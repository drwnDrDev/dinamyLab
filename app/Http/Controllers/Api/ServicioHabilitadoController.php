<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ServicioHabilitado;
use Illuminate\Http\Request;

class ServicioHabilitadoController extends Controller
{
    public function index()
    {
        $servicios = ServicioHabilitado::orderBy('nivel','desc')
                        ->get();

        if ($servicios->isEmpty()) {
            return response()->json(['message' => 'No hay servicios habilitados activos'], 204);
        }

        return response()->json([
            "message" => "Servicios Habilitados activos",
             "data"=>  $servicios
        ], 200);
    }
    public function show($codigo)
    {
        $servicio = ServicioHabilitado::where('codigo', $codigo)
                        ->where('activo', true)
                        ->first();

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json(
            [
                "message" => "Servicio Habilitado encontrado",
                "data" => $servicio
            ], 200
        );
    }

    public function buscarPorNombre(Request $request)
    {
        $nombre = $request->query('nombre');

        if (!$nombre) {
            return response()->json(['message' => 'El parámetro nombre es obligatorio'], 400);
        }

        $servicios = ServicioHabilitado::where('nombre', 'LIKE', '%' . $nombre . '%')
                        ->where('activo', true)
                        ->orderBy('nivel','desc')
                        ->get();

        if ($servicios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron servicios con ese nombre'], 204);
        }

        return response()->json([
            "message" => "Servicios Habilitados encontrados",
             "data"=>  $servicios
        ], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'codigo' => 'required|unique:servicios_habilitados,codigo',
            'nombre' => 'required|string|max:255',
            'grupo' => 'nullable|string|max:255',
            'codigo_grupo' => 'nullable|integer',
            'activo' => 'boolean',
            'nivel' => 'nullable|integer|min:1|max:10',
        ]);

        $servicio = ServicioHabilitado::create($validatedData);

        return response()->json([
            "message" => "Servicio Habilitado creado exitosamente",
            "data" => $servicio
        ], 201);
    }
    public function update(Request $request, $codigo)
    {
        $servicio = ServicioHabilitado::where('codigo', $codigo)->first();

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'grupo' => 'sometimes|nullable|string|max:255',
            'codigo_grupo' => 'sometimes|nullable|integer',
            'activo' => 'sometimes|boolean',
            'nivel' => 'sometimes|nullable|integer|min:1|max:10',
        ]);

        $servicio->update($validatedData);

        return response()->json([
            "message" => "Servicio Habilitado actualizado exitosamente",
            "data" => $servicio
        ], 200);
    }
    public function destroy($codigo)
    {
        $servicio = ServicioHabilitado::where('codigo', $codigo)->first();

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        $servicio->delete();

        return response()->json(['message' => 'Servicio Habilitado eliminado exitosamente'], 200);
    }

}
