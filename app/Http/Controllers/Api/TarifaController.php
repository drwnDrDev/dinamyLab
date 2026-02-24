<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = Tarifa::query();

        if (request()->filled('tarifable_type')) {
            $tarifas->where('tarifable_type', $this->normalizeTarifableType(request()->input('tarifable_type')));
        }

        if (request()->filled('sede_id')) {
            $sedeId = (int) request()->input('sede_id');
            $tarifas->whereIn('id', function ($query) use ($sedeId) {
                $query->select('tarifa_id')
                    ->from('sede_tarifa')
                    ->where('sede_id', $sedeId);
            });
        }

        return response()->json($tarifas->get());
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
        $data = $request->all();
        if (isset($data['tarifable_type'])) {
            $data['tarifable_type'] = $this->normalizeTarifableType($data['tarifable_type']);
        }

        $tarifa = Tarifa::create($data);

        if ($request->filled('sede_id')) {
            DB::table('sede_tarifa')->updateOrInsert([
                'sede_id' => $request->input('sede_id'),
                'tarifa_id' => $tarifa->id,
            ]);
        }

        if($request->filled('convenio_id')) {
            DB::table('convenio_tarifa')->updateOrInsert([
                'convenio_id' => $request->input('convenio_id'),
                'tarifa_id' => $tarifa->id,
            ]);
        }
        
        return response()->json($tarifa, 201);
    }
    public function update(Request $request, $id)
    {
        $tarifa = Tarifa::find($id);
        if ($tarifa) {
            $data = $request->except('sede_id');
            if (isset($data['tarifable_type'])) {
                $data['tarifable_type'] = $this->normalizeTarifableType($data['tarifable_type']);
            }

            $tarifa->update($data);

            if ($request->filled('sede_id')) {
                DB::table('sede_tarifa')->updateOrInsert([
                    'sede_id' => $request->input('sede_id'),
                    'tarifa_id' => $tarifa->id,
                ]);
            }
            return response()->json($tarifa);
        } else {
            return response()->json(['message' => 'Tarifa no encontrada'], 404);
        }
    }

    private function normalizeTarifableType(string $tarifableType): string
    {
        if ($tarifableType === 'examen') {
            return Examen::class;
        }

        return $tarifableType;
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
