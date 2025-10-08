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
        try {
            // Validar los datos de entrada
            $validated = $request->validate([
                'paciente_id' => 'required|exists:pacientes,id',
                'sede_id' => 'required|exists:sedes,id',
                'numero_orden' => 'required|integer',
                'fecha_orden' => 'required|date',
                'examenes' => 'required|array',
                'examenes.*.id' => 'required|exists:examenes,id',
                'examenes.*.cantidad' => 'required|integer|min:1',
                'mopdalidad' => 'required|string',
                'finalidad' => 'required|string',
                'via_ingreso' => 'required|string',
                'cie_principal' => 'nullable|string',
                'cie_relacionado' => 'nullable|string',
            ]);

        
        // Crear la orden
        $orden = Orden::create([
            'paciente_id' => $request->input('paciente_id'),
            'sede_id' => $request->input('sede_id'),
            'numero' => $request->input('numero'),
            'fecha' => $request->input('fecha'),
            'user_id' => Auth::id(),
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
            return response()->json(['message' => 'Orden creada correctamente.', 'orden_id' => $orden->id], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
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
