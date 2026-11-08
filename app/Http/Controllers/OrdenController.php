<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Orden;
use App\Models\Examen;
use App\Models\Persona;
use App\Services\ElegirEmpresa;
use App\Services\SugerirNumeroOrden;
use App\Models\MetodoPago;
use App\Models\Sede;
use App\Models\TipoDocumento;
use App\Http\Requests\OrdenStoreRequest;
use App\Models\ContactoEmergencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Procedimiento;
use App\Services\ControlSede;
use Illuminate\Support\Facades\DB;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sede = $request->user()->hasRole('admin')
            ? $request->session()->get('sede')
            : ControlSede::autorizarSedeActiva($request);
        $filters = $request->validate([
            'busqueda' => 'nullable|string|max:100',
            'estado' => 'nullable|in:pendiente,completada',
        ]);
        $busqueda = trim($filters['busqueda'] ?? '');
        $query = Orden::with('paciente')
            ->when(! $request->user()->hasRole('admin'), fn ($query) => $query->where('sede_id', $sede->id));

        if ($busqueda !== '') {
            $query->where(function ($query) use ($busqueda) {
                $query->where('numero', 'like', "%{$busqueda}%")
                    ->orWhereHas('paciente', function ($paciente) use ($busqueda) {
                        $paciente->where('numero_documento', 'like', "%{$busqueda}%")
                            ->orWhere('primer_nombre', 'like', "%{$busqueda}%")
                            ->orWhere('segundo_nombre', 'like', "%{$busqueda}%")
                            ->orWhere('primer_apellido', 'like', "%{$busqueda}%")
                            ->orWhere('segundo_apellido', 'like', "%{$busqueda}%");
                    });
            });
        }

        $query->when(($filters['estado'] ?? null) === 'pendiente', fn ($query) => $query->whereNull('terminada'));
        $query->when(($filters['estado'] ?? null) === 'completada', fn ($query) => $query->whereNotNull('terminada'));
        $ordenes = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Persona $persona = null)
    {

        $sede = ElegirEmpresa::elegirSede();
        if (!$sede) {
            return redirect()->route('dashboard')
                ->with('error', 'Selecciona una sede antes de crear una orden médica.');
        }
        $examenes = Examen::all();
        $sedes = Sede::query()->orderBy('nombre')->get(['id', 'nombre']);
        $orden_numero = SugerirNumeroOrden::paraSede($sede->id);
        return view('ordenes.create', compact('examenes', 'orden_numero', 'persona', 'sedes'));
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
        ControlSede::autorizarSedeActiva($request);

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
        ControlSede::autorizarOrden(request(), $orden);
        $orden->load(['paciente','examenes','procedimientos', 'remisiones.destinoSede']);
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
        ControlSede::autorizarOrden(request(), $orden);
        return view('ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $orden)
    {
        ControlSede::autorizarOrden($request, $orden);

        $validated = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'abono' => ['nullable', 'numeric', 'min:0'],
        ]);
        $orden->update($validated);

        return redirect()->route('ordenes.show', $orden)->with('success', 'Orden actualizada correctamente.');
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
        ControlSede::autorizarOrden(request(), $orden);
        DB::transaction(function () use ($orden) {
            $orden->procedimientos()->get()->each->delete();
            $orden->delete();
        });

        return redirect()->route('ordenes')->with('success', 'Orden médica eliminada correctamente');
    }
}
