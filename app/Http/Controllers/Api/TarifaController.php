<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarifa;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = Tarifa::all();
        return response()->json($tarifas);
    }
    public function show($id)
    {
        $tarifa = Tarifa::find($id);
        if ($tarifa) {
            return response()->json($tarifa);
        } else {
            return response()->json(['message' => 'Tarifa no encontrada'], 404);
        }
    }
    public function store(Request $request)
    {
        $tarifa = Tarifa::create($request->all());
        return response()->json($tarifa, 201);
    }
    public function update(Request $request, $id)
    {
        $tarifa = Tarifa::find($id);
        if ($tarifa) {
            $tarifa->update($request->all());
            return response()->json($tarifa);
        } else {
            return response()->json(['message' => 'Tarifa no encontrada'], 404);
        }
    }
    public function destroy($id)
    {

        
        $tarifa = Tarifa::find($id);
        if ($tarifa) {
            $tarifa->delete();
            return response()->json(['message' => 'Tarifa eliminada']);
        } else {
            return response()->json(['message' => 'Tarifa no encontrada'], 404);
        }
    }
}
