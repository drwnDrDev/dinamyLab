<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Orden;
use App\Models\Examen;
use App\Models\Persona;

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
            ->where('terminada', null) // Solo mostrar órdenes que no están terminadas
            ->where('created_at', '>=', now()->subDays(3)) // los ultimos 3 dias
            ->orderBy('created_at', 'desc')
            ->get();
        if ($ordenes->isEmpty()) {
            return to_route('ordenes.create')
                ->with('info', 'No hay órdenes médicas recientes. Crea una nueva orden.');
        }
        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $sede = session('sede');
        if (!$sede) {
            return redirect()->back()->withErrors(['sede' => 'No se ha seleccionado una sede.'])->withInput();
        }
        $examenes = Examen::all();
        $orden_numero = Orden::where('sede_id', $sede->id)
               ->max('numero') ?  : 1; // Si no hay órdenes, iniciar en 1
        $orden_numero = str_pad($orden_numero + 1, 5, '0', STR_PAD_LEFT); // Formatear el número de orden con ceros a la izquierda
        return view('ordenes.create', compact('examenes', 'orden_numero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:personas,id',
            'acompaniante_id' => 'nullable|exists:personas,id',
            'numero_orden' => 'required|string|max:20|unique:ordenes_medicas,numero',
            'examenes' => 'array', // Asegura que 'examenes' es un array
        ]);

        $sede = session('sede');
        if (!$sede) {
            return redirect()->back()->withErrors(['sede' => 'No se ha seleccionado una sede.'])->withInput();
        }

        $paciente = Persona::findOrFail($request->input('paciente_id'));

        $prueba = Examen::where('cup','904508')->first('id');

        $examenesSolicitados = array_filter(
            $request->input('examenes', []),
            fn ($cantidad) => !is_null($cantidad) && $cantidad != 0
        );

        // Si no se seleccionó ningún examen válido, redirige con un error.
        if (empty($examenesSolicitados)) {
            return redirect()->back()->withErrors(['examenes' => 'Debe seleccionar al menos un examen.'])->withInput();
        }

        if(($paciente->edad()<10 || $paciente->sexo ==='M') &&  in_array($prueba->id, array_keys($examenesSolicitados)) ){
             return redirect()->back()->withErrors(['examenes' => "Prueba de embarazo no viable para el paciente"])->withInput();
        }

        $examenesData = Examen::whereIn('id', array_keys($examenesSolicitados))
            ->get()
            ->keyBy('id');

        $orden_examen = [];
        foreach ($examenesSolicitados as $examenId => $cantidad) {
            $examen = $examenesData->get($examenId);
            if (!$examen) {
                return redirect()->back()->withErrors(['examenes' => "El examen con ID {$examenId} no se pudo encontrar."])->withInput();
            }
            array_push($orden_examen,[
                'examen_id'=>$examenId,
                'cantidad'=>$cantidad
            ]);

        }

        DB::transaction(function () use ($request, $paciente, $examenesSolicitados, $examenesData,$orden_examen, $sede) {

            $total = $examenesData->sum(function ($examen) use ($examenesSolicitados) {
                return $examen->valor * $examenesSolicitados[$examen->id];
            });

            $abono = $request->input('pago') === "on" ? $total : $request->input('abono', 0);

            $orden = Orden::create([
                'sede_id' => $sede->id, // Asumiendo que el usuario autenticado tiene una sede asociada
                'numero' => $request->input('numero_orden'),
                'paciente_id' => $paciente->id, // Usar el ID del paciente cargado
                'acompaniante_id' => $request->input('acompaniante_id'),
                'descripcion' => $request->input('observaciones'),
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
                        'sede_id' => $sede->id, // Asumiendo que el usuario autenticado tiene una sede asociada
                        'estado' => Estado::PROCESO, // Asumo que Estado::PROCESO es una constante o Enum
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Procedimiento::insert($procedimientosParaInsertar);
        }); // Fin de la transacción

        return redirect()->route('procedimientos')->with('success', 'Orden médica creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        $orden->load(['paciente','examenes','procedimientos']);


        return view('ordenes.show', compact('orden'));
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
        if (Auth::user()->can('eliminar_orden_medica')) {
            $orden->delete();
            return redirect()->route('ordenes')->with('success', 'Orden médica eliminada correctamente');
        }
        return redirect()->route('ordenes')->with('error', 'No tienes permiso para eliminar esta orden médica');
    }
}
