<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orden;
use App\Estado;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\SugerirNumeroOrden;
use App\Services\ControlSede;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $sede = ControlSede::autorizarSedeActiva($request);

        $validated = $request->validate([
            'paciente_id' => 'required|exists:personas,id',
            'numero_orden' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ordenes_medicas', 'numero')
                    ->where('sede_id', $sede->id),
            ],
            'fecha_orden' => 'required|date',
            'examenes' => 'required|array',
            'examenes.*.id' => 'required|exists:examenes,id',
            'examenes.*.cantidad' => 'required|integer|min:1',
            'modalidad' => 'required|string',
            'finalidad' => 'required|string',
            'via_ingreso' => 'required|string',
            'cie_principal' => 'nullable|string',
            'cie_relacionado' => 'nullable|string',
        ]);



        // Calcular el total de la orden
        $total = collect($request->input('examenes'))->sum(function ($examen) {
            return $examen['valor'] * $examen['cantidad'];
        });

        $usuario = Auth::user();
        $ordenCreada =  DB::transaction(function () use ( $usuario, $validated, $total, $sede) {
            // Crear la orden médica
            $orden = Orden::create([
                'sede_id' => $sede->id,
                'numero' => $validated['numero_orden'],
                'user_id' => $usuario ? $usuario->id : 1,
                'paciente_id' => $validated['paciente_id'],
                'codigo_recaudo' => '05', // Consulta externa
                'observaciones' => $validated['observaciones'] ?? null,
                'abono' => $validated['abono'] ?? 0,
                'total' => $total,
            ]);

            // Asociar los exámenes a la orden y crear los procedimientos
            foreach ($validated['examenes'] as $examenData) {
                $orden->examenes()->attach(
                    $examenData['id'],
                    ['cantidad' => $examenData['cantidad']]
                );


            // Crear los procedimientos asociados a la orden
            $procedimientos = [];
            for ($i = 0; $i < $examenData['cantidad']; $i++) {
                $procedimientos[] = [
                    'orden_id' => $orden->id,
                        'examen_id' => $examenData['id'],
                        'diagnostico_principal' => $validated['cie_principal'] ?? 'Z017', // Asignar un valor por defecto si no se proporciona
                        'diagnostico_relacionado' => $validated['cie_relacionado'] ?? null,
                        'codigo_modalidad' => $validated[ 'modalidad']??'01',
                        'codigo_finalidad' => $validated['finalidad']??'15',
                        'codigo_via_ingreso' => $validated['via_ingreso']??'01',
                        'sede_id' => $sede->id,
                        'fecha' => now(),
                        'estado' => Estado::PROCESO,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

            DB::table('procedimientos')->insert($procedimientos);
            }
            return $orden; // Retorna la orden creada para usarla fuera de la transacción

        });

        return response()->json(['message' => 'Orden creada correctamente.', 'data' => $ordenCreada], 201);


    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orden = Orden::find($id);

        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        ControlSede::autorizarOrden(request(), $orden);
        $orden->load(['paciente', 'procedimientos.examen']);

        return response()->json($orden);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function add(Request $request, string $id)
    {
        $orden = Orden::findOrFail($id);
        ControlSede::autorizarOrden($request, $orden);
        $request->validate([
            'examen_id' => 'required|exists:examenes,id',
            'cantidad' => 'required|integer|min:1',
        ]);
        $examen_id = $request->input('examen_id');
        $cantidad = $request->input('cantidad');
        $orden->examenes()->attach($examen_id, [
            'cantidad' => $cantidad,

        ]);
        // Crear los procedimientos asociados a la orden
        $procedimientos = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $procedimientos[] = [
                'orden_id' => $orden->id,

                'examen_id' => $examen_id,
                'fecha' => now(),
                'estado' => Estado::PROCESO,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $orden->procedimientos()->createMany($procedimientos);
        return response()->json(['message' => 'Procedimiento agregado a la orden correctamente.']);
    }

    public function max_numero(Request $request)
    {
        $sede = ControlSede::autorizarSedeActiva($request);
        $siguienteNumero = SugerirNumeroOrden::paraSede($sede->id);
        return response()->json([
            'next_numero' => $siguienteNumero,
            'sesion_sede' => true,
        ]);
    }

    public function verificarNumero(Request $request)
    {
        $validated = $request->validate([
            'numero_orden' => 'required|string|max:255',
        ]);
        $sede = ControlSede::autorizarSedeActiva($request);
        $sedeId = $sede->id;
        $numero = trim($validated['numero_orden']);

        return response()->json([
            'usado' => Orden::withTrashed()->where('sede_id', $sedeId)->where('numero', $numero)->exists(),
            'sugerencia' => SugerirNumeroOrden::paraSede($sedeId),
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orden = Orden::findOrFail($id);
        ControlSede::autorizarOrden(request(), $orden);
        DB::transaction(function () use ($orden) {
            $orden->procedimientos()->get()->each->delete();
            $orden->delete();
        });

        return response()->json(['message' => 'Orden eliminada correctamente.'], 200);
    }
}
