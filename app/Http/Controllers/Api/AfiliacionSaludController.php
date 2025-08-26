<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\AfiliacionSalud;

class AfiliacionSaludController extends Controller
{
    public function index()
    {
        $totalEPS = AfiliacionSalud::groupBy('eps')->selectRaw('count(*) as total, eps')->get();
        return response()->json([
            'message' => 'Total de EPS por afiliación',
            'data' => $totalEPS
        ]);
    }

    public function show($id)
    {
        $afiliacion = AfiliacionSalud::where('persona_id', $id)->first();

        if (!$afiliacion) {
            return response()->json([
                'message' => 'Afiliación de salud no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Afiliación de salud encontrada',
            'data' => $afiliacion
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'eps' => 'required|string|max:255',
            'tipo_afiliacion' => 'required|exists:tipos_afiliaciones,codigo',
            'persona_id' => 'required|exists:personas,id'
        ]);

        $afiliacion = AfiliacionSalud::create($request->all());

        return response()->json([
            'message' => 'Afiliación de salud creada con éxito',
            'data' => $afiliacion
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'eps' => 'sometimes|required|string|max:255',
            'tipo_afiliacion' => 'sometimes|required|exists:tipos_afiliaciones,codigo',
            'persona_id' => 'sometimes|required|exists:personas,id'
        ]);

        $afiliacion = AfiliacionSalud::find($id);

        if (!$afiliacion) {
            return response()->json([
                'message' => 'Afiliación de salud no encontrada',
                'data' => null
            ], 404);
        }

        $afiliacion->update($request->all());

        return response()->json([
            'message' => 'Afiliación de salud actualizada con éxito',
            'data' => $afiliacion
        ]);
    }

    public function destroy($id)
    {
        Authorization::check('delete_afiliacion_salud');

        $afiliacion = AfiliacionSalud::find($id);

        if (!$afiliacion) {
            return response()->json([
                'message' => 'Afiliación de salud no encontrada',
                'data' => null
            ], 404);
        }

        $afiliacion->delete();

        return response()->json([
            'message' => 'Afiliación de salud eliminada con éxito',
            'data' => null
        ]);
    }
}
