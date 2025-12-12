<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Orden;
use App\Models\Examen;
use App\Models\Persona;
use App\Services\ElegirEmpresa;
use App\Models\MetodoPago;
use App\Models\TipoDocumento;
use App\Http\Requests\OrdenStoreRequest;
use App\Models\ContactoEmergencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Procedimiento;
use Illuminate\Support\Facades\DB;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $sede = session('sede');

        if (!$sede) {
            return redirect()->back()->withErrors(['sede' => 'No se ha seleccionado una sede.'])->withInput();
        }
        $ordenes = Orden::with(['paciente'])
            ->where('sede_id', $sede->id)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        if ($ordenes->isEmpty()) {
            return redirect()->route('ordenes.create')->with('error', 'lista de ordenes vacias');
        }
        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $sede = ElegirEmpresa::elegirSede();
        if (!$sede) {
            return redirect()->back()->withErrors(['sede' => 'No se ha seleccionado una sede.'])->withInput();
        }
        $examenes = Examen::all();
        $orden_numero = Orden::where('sede_id', $sede->id)
               ->max('numero') ?  : 0; // Si no hay órdenes, iniciar en 0
        if (Orden::where('numero', $orden_numero+1)->exists()) {
            // Si ya existe el número de orden, comenzar en la siguiente unidad de mil
            $maxNumero = Orden::max('numero');
            $orden_numero =( ceil(($maxNumero + 1) / 2000) * 2000);
        }else {
            $orden_numero = $orden_numero + 1; // Incrementar para la nueva orden
        }
        return view('ordenes.create', compact('examenes', 'orden_numero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrdenStoreRequest $request)
    {

        $request->validated(); // Validar los datos del formulario
        $sede = ElegirEmpresa::elegirSede();

        if (!$sede) {
            return redirect()->back()->withErrors(['sede' => 'No se ha seleccionado una sede.'])->withInput();
        }

        $paciente = Persona::findOrFail($request->input('paciente_id'));

        if (!$paciente) {
            return redirect()->back()->withErrors(['paciente' => 'Paciente no encontrado.'])->withInput();
        }

        // Consolidar los exámenes solicitados en una sola pasada
        $examenesSolicitados = collect($request->input('examenes', []))
            ->filter(fn($cantidad) => !is_null($cantidad) && $cantidad != 0);

        if ($examenesSolicitados->isEmpty()) {
            return redirect()->back()->withErrors(['examenes' => 'Debe seleccionar al menos un examen.'])->withInput();
        }

        $examenesData = Examen::whereIn('id', $examenesSolicitados->keys())->get()->keyBy('id');

        // Usar map para construir el array de orden_examen
        $orden_examen = $examenesSolicitados->map(function ($cantidad, $examenId) use ($examenesData) {
            if (!$examenesData->has($examenId)) {
                throw new \Exception("El examen con ID {$examenId} no se pudo encontrar.");
            }
            return [
                'examen_id' => $examenId,
                'cantidad' => $cantidad
            ];
        })->values()->toArray();
        $contactoEmergencia = null;
        if ($request->input('acompaniante_id')) {
            $acompaniante = Persona::findOrFail($request->input('acompaniante_id'));
            if ($acompaniante) {
                $contactoEmergencia = ContactoEmergencia::updateOrCreate(
                    ['acompanante_id'=>$acompaniante->id, 'paciente_id' => $paciente->id],
                    [
                        'parentesco' => $request->input('parentesco_acompaniante', 'Otro'),
                    ]
                );
            }
        } else {
            $acompaniante = null;
        }

        $ordenCreada =  DB::transaction(function () use ($request, $paciente,$contactoEmergencia, $examenesSolicitados, $examenesData,$orden_examen, $sede) {

            $total = $examenesData->sum(function ($examen) use ($examenesSolicitados) {
                return $examen->valor * $examenesSolicitados[$examen->id];
            });

            $abono = $request->input('pago') === "on" ? $total : $request->input('abono', 0);

            $orden = Orden::create([
                'sede_id' => $sede->id, // Asumiendo que el usuario autenticado tiene una sede asociada
                'numero' => $request->input('numero_orden'),
                'paciente_id' => $paciente->id, // Usar el ID del paciente cargado

                'observaciones' => $request->input('observaciones'),
                'abono' => $abono,
                'total' => $total,
            ]);
            $orden->examenes()->attach($orden_examen);
            $procedimientosParaInsertar = [];
            foreach ($examenesSolicitados as $examenId => $cantidad) {
                for ($i = 0; $i < $cantidad; $i++) {
                    $procedimientosParaInsertar[] = [
                        'orden_id' => $orden->id, // ID de la orden recién creada
                        'empleado_id' => Auth::user()->id, // ID del empleado/doctor autenticado
                        'examen_id' => $examenId,
                        'fecha' => now(),
                        'sede_id' => $sede->id,
                        'contacto_emergencia_id' => $contactoEmergencia? $contactoEmergencia->id : null, // ID del contacto de emergencia si existe
                        'estado' => Estado::PROCESO, // Asumo que Estado::PROCESO es una constante o Enum
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Procedimiento::insert($procedimientosParaInsertar);
            return $orden; // Retorna la orden creada para usarla fuera de la transacción
        }); // Fin de la transacción

        return response()->json(['data' => $ordenCreada], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        $orden->load(['paciente','examenes','procedimientos']);
        if (!$orden) {
            return redirect()->route('ordenes')->with('error', 'Orden médica no encontrada');
        }
        $mediosPago = MetodoPago::orderBy('nivel', 'desc')->get();
        return view('ordenes.show', compact('orden', 'mediosPago'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orden $orden)
    {

        return view('ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $orden)
    {
        //
    }

    public function resultados(Orden $orden,Examen $examen)
    {

        $orden->load(['paciente', 'acompaniante', 'examenes']);
        return view('procedimientos.resultados', compact('orden', 'examen'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orden $orden)
    {
        if (\Illuminate\Support\Facades\Gate::allows('eliminar_orden_medica')) {
            $orden->delete();
            return redirect()->route('ordenes')->with('success', 'Orden médica eliminada correctamente');
        }
        return redirect()->route('ordenes')->with('error', 'No tienes permiso para eliminar esta orden médica');
    }
}
