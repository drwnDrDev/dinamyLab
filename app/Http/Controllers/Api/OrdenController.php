<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orden;
use App\Estado;

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


            $validated = $request->validate([
                'paciente_id' => 'required|exists:pacientes,id',
                'numero_orden' => 'required|integer',
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

        //mostrar errores de validacion
        if ($validated->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validated->errors(),
            ], 422);
        }

        // Calcular el total de la orden
        $total = $request->input('examenes')->sum(function ($examen) {
            return $examen['valor'] * $examen['cantidad'];
        });

        if($request->input('total', 0) !== $total && $request->input('observaciones', '') == ''){
            return response()->json([
                'message' => 'El total de la orden no coincide con la suma de los exámenes.',
            ], 422);
        }



        // Crear la orden médica
        $orden = Orden::create([
            'sede_id' => session('sede')->id,
            'numero' => $request->input('numero_orden'),
            'user_id' => $request->user()->id,
            'paciente_id' => $request->input('paciente_id'),
            'codigo_recaudo' => '05', // Consulta externa
            'observaciones' => $request->input('observaciones', null),
            'abono' => $request->input('abono', null),
            'total' => $request->input('total'),
        ]);

        // Asociar los exámenes a la orden y crear los procedimientos
        foreach ($request->input('examenes') as $examenData) {
            $orden->examenes()->attach($examenData['id'], [
                'cantidad' => $examenData['cantidad'],
            ]);
            // Crear los procedimientos asociados a la orden
            $procedimientos = [];
            for ($i = 0; $i < $examenData['cantidad']; $i++) {
                $procedimientos[] = [
                    'orden_id' => $orden->id,
                    'examen_id' => $examenData['id'],
                    'fecha' => now(),
                    'estado' => Estado::PROCESO,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('procedimientos')->insert($procedimientos);
        }
        return response()->json(['message' => 'Orden creada correctamente.', 'orden_id' => $orden->id], 201);

    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


        $orden = Orden::findOrFail($id);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
