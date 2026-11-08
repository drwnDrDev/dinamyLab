<?php

namespace App\Http\Controllers\Api;

use App\Estado;
use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Sede;
use App\Services\ControlSede;
use App\Services\SugerirNumeroOrden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        $rules = [
            'paciente_id' => 'required|exists:personas,id',
            'numero_orden' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ordenes_medicas', 'numero')
                    ->where('sede_id', $sede->id),
            ],
            'fecha_orden' => 'required|date',
            'examenes' => 'required_without:remisiones|array',
            'examenes.*.id' => 'required|exists:examenes,id',
            'examenes.*.cantidad' => 'required|integer|min:1',
            'remisiones' => 'sometimes|array',
            'remisiones.*.cup' => 'required|string|max:20',
            'remisiones.*.nombre' => 'required|string|max:255',
            'remisiones.*.valor' => 'required|numeric|min:0',
            'remisiones.*.destino_sede_id' => 'nullable|integer|exists:sedes,id',
            'remisiones.*.destino' => 'required|string|max:255',
            'abono' => 'nullable|numeric|min:0',
        ];

        if (empty($request->input('remisiones'))) {
            $rules += [
                'modalidad' => 'required|string',
                'finalidad' => 'required|string',
                'via_ingreso' => 'required|string',
                'cie_principal' => 'nullable|string',
                'cie_relacionado' => 'nullable|string',
            ];
        }

        $validated = $request->validate($rules);
        $examenes = $validated['examenes'] ?? [];
        $remisiones = $validated['remisiones'] ?? [];

        if ($examenes === [] && $remisiones === []) {
            throw ValidationException::withMessages([
                'examenes' => 'Debe agregar al menos un examen o una remisión.',
            ]);
        }

        $examenesData = Examen::whereIn('id', collect($examenes)->pluck('id'))->get()->keyBy('id');
        $totalExamenes = collect($examenes)->sum(
            fn ($examen) => $examenesData[$examen['id']]->valor * $examen['cantidad']
        );
        $totalRemisiones = collect($remisiones)->sum('valor');
        $total = $totalExamenes + $totalRemisiones;
        $abono = $validated['abono'] ?? 0;

        if ($abono > $total) {
            throw ValidationException::withMessages([
                'abono' => 'El abono no puede ser mayor que el total de la orden.',
            ]);
        }

        $usuario = Auth::user();
        $ordenCreada = DB::transaction(function () use ($usuario, $validated, $examenes, $remisiones, $total, $abono, $sede) {
            // Crear la orden médica
            $orden = Orden::create([
                'sede_id' => $sede->id,
                'numero' => $validated['numero_orden'],
                'user_id' => $usuario->id,
                'paciente_id' => $validated['paciente_id'],
                'codigo_recaudo' => '05', // Consulta externa
                'observaciones' => $validated['observaciones'] ?? null,
                'abono' => $abono,
                'total' => $total,
            ]);

            // Asociar los exámenes a la orden y crear los procedimientos
            $procedimientos = [];
            foreach ($examenes as $examenData) {
                $orden->examenes()->attach(
                    $examenData['id'],
                    ['cantidad' => $examenData['cantidad']]
                );

                for ($i = 0; $i < $examenData['cantidad']; $i++) {
                    $procedimientos[] = [
                        'orden_id' => $orden->id,
                        'examen_id' => $examenData['id'],
                        'diagnostico_principal' => $validated['cie_principal'] ?? 'Z017', // Asignar un valor por defecto si no se proporciona
                        'diagnostico_relacionado' => $validated['cie_relacionado'] ?? null,
                        'codigo_modalidad' => $validated['modalidad'] ?? '01',
                        'codigo_finalidad' => $validated['finalidad'] ?? '15',
                        'codigo_via_ingreso' => $validated['via_ingreso'] ?? '01',
                        'sede_id' => $sede->id,
                        'fecha' => now(),
                        'estado' => Estado::PROCESO,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if ($procedimientos !== []) {
                DB::table('procedimientos')->insert($procedimientos);
            }

            if ($remisiones !== []) {
                $orden->remisiones()->createMany($remisiones);
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
        $orden->load(['paciente', 'procedimientos.examen', 'remisiones.destinoSede']);

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
