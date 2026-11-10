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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        $orden->load(['paciente', 'examenes', 'procedimientos', 'remisiones.destinoSede']);
        $examenes = Examen::query()->orderBy('nombre')->get(['id', 'nombre', 'valor']);
        $sedes = Sede::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('ordenes.edit', compact('orden', 'examenes', 'sedes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $orden)
    {
        ControlSede::autorizarOrden($request, $orden);

        $nuevasRemisiones = collect($request->input('nuevas_remisiones', []))
            ->filter(fn ($remision) => collect($remision)->contains(fn ($valor) => filled($valor)))
            ->all();
        $nuevosExamenes = collect($request->input('examenes_nuevos', []))
            ->filter(fn ($examen) => collect($examen)->contains(fn ($valor) => filled($valor)))
            ->all();
        $request->merge([
            'nuevas_remisiones' => $nuevasRemisiones,
            'examenes_nuevos' => $nuevosExamenes,
        ]);

        $examenesActuales = $orden->examenes()->pluck('examenes.id')->all();
        $remisionesActuales = $orden->remisiones()->pluck('id')->all();
        $validated = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'abono' => ['nullable', 'numeric', 'min:0'],
            'examenes' => $examenesActuales === [] ? ['nullable', 'array'] : ['required', 'array'],
            'examenes.*' => ['required', 'integer', 'min:0', 'max:255'],
            'examenes_nuevos' => ['nullable', 'array'],
            'examenes_nuevos.*.id' => ['required', 'integer', 'exists:examenes,id', 'distinct'],
            'examenes_nuevos.*.cantidad' => ['required', 'integer', 'min:1', 'max:255'],
            'remisiones' => $remisionesActuales === [] ? ['nullable', 'array'] : ['required', 'array'],
            'remisiones.*.id' => [
                'required',
                'distinct',
                'integer',
                Rule::exists('orden_remisiones', 'id')->where('orden_id', $orden->id),
            ],
            'remisiones.*.destino_sede_id' => ['nullable', 'integer', 'exists:sedes,id'],
            'remisiones.*.destino' => ['nullable', 'string', 'max:255'],
            'remisiones.*.eliminar' => ['nullable', 'boolean'],
            'nuevas_remisiones' => ['nullable', 'array'],
            'nuevas_remisiones.*.cup' => ['required', 'string', 'max:20'],
            'nuevas_remisiones.*.nombre' => ['required', 'string', 'max:255'],
            'nuevas_remisiones.*.valor' => ['required', 'numeric', 'min:0'],
            'nuevas_remisiones.*.destino_sede_id' => ['nullable', 'integer', 'exists:sedes,id'],
            'nuevas_remisiones.*.destino' => ['nullable', 'string', 'max:255'],
        ]);

        $idsExamenesEnviados = array_map('intval', array_keys($validated['examenes'] ?? []));
        if (array_diff($examenesActuales, $idsExamenesEnviados) !== [] || array_diff($idsExamenesEnviados, $examenesActuales) !== []) {
            throw ValidationException::withMessages([
                'examenes' => 'Actualice la lista de exámenes antes de guardar la orden.',
            ]);
        }

        $idsRemisionesEnviadas = collect($validated['remisiones'] ?? [])->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (array_diff($remisionesActuales, $idsRemisionesEnviadas) !== [] || array_diff($idsRemisionesEnviadas, $remisionesActuales) !== []) {
            throw ValidationException::withMessages([
                'remisiones' => 'Actualice la lista de remisiones antes de guardar la orden.',
            ]);
        }

        $idsExamenesNuevos = collect($validated['examenes_nuevos'] ?? [])->pluck('id')->all();
        if (array_intersect($examenesActuales, $idsExamenesNuevos) !== []) {
            throw ValidationException::withMessages([
                'examenes_nuevos' => 'Un examen que ya está en la orden no se puede agregar de nuevo.',
            ]);
        }

        $cantidades = collect($validated['examenes'] ?? [])
            ->mapWithKeys(fn ($cantidad, $id) => [(int) $id => (int) $cantidad])
            ->only($examenesActuales);
        foreach ($validated['examenes_nuevos'] ?? [] as $examenNuevo) {
            $cantidades[(int) $examenNuevo['id']] = (int) $examenNuevo['cantidad'];
        }

        $remisionesEnviadas = collect($validated['remisiones'] ?? [])->keyBy('id');
        $remisionesNuevas = $validated['nuevas_remisiones'] ?? [];

        DB::transaction(function () use ($orden, $validated, $cantidades, $remisionesEnviadas, $remisionesNuevas) {
            $orden = Orden::query()->whereKey($orden->id)->lockForUpdate()->firstOrFail();
            $procedimientos = $orden->procedimientos()->lockForUpdate()->get()->groupBy('examen_id');
            $plantilla = $procedimientos->flatten()->first();
            $examenes = Examen::query()->whereIn('id', $cantidades->keys())->get()->keyBy('id');
            $cantidadesPivot = [];

            foreach ($cantidades as $examenId => $cantidad) {
                $existentes = $procedimientos->get($examenId, collect());
                $terminados = $existentes->filter(fn ($procedimiento) => in_array($procedimiento->estado, [
                    Estado::TERMINADO->value,
                    Estado::ENTREGADO->value,
                ], true) || $procedimiento->resultado()->exists());

                if ($cantidad < $terminados->count()) {
                    throw ValidationException::withMessages([
                        "examenes.{$examenId}" => "No se puede quitar este examen porque tiene {$terminados->count()} procedimiento(s) con resultados o terminados. Se conservan sus resultados; cree una nueva orden para solicitar otros exámenes.",
                    ]);
                }

                if ($cantidad < $existentes->count()) {
                    $porQuitar = $existentes
                        ->reject(fn ($procedimiento) => $terminados->contains('id', $procedimiento->id))
                        ->take($existentes->count() - $cantidad);
                    $porQuitar->each->delete();
                } elseif ($cantidad > $existentes->count()) {
                    for ($i = $existentes->count(); $i < $cantidad; $i++) {
                        $orden->procedimientos()->create([
                            'examen_id' => $examenId,
                            'fecha' => now(),
                            'sede_id' => $orden->sede_id,
                            'diagnostico_principal' => $plantilla?->diagnostico_principal ?? 'Z017',
                            'diagnostico_relacionado' => $plantilla?->diagnostico_relacionado,
                            'codigo_servicio' => $plantilla?->codigo_servicio ?? 706,
                            'codigo_finalidad' => $plantilla?->codigo_finalidad ?? '15',
                            'codigo_modalidad' => $plantilla?->codigo_modalidad ?? '01',
                            'codigo_via_ingreso' => $plantilla?->codigo_via_ingreso ?? '01',
                            'estado' => Estado::PROCESO->value,
                        ]);
                    }
                }

                if ($cantidad > 0) {
                    $cantidadesPivot[$examenId] = ['cantidad' => $cantidad];
                }
            }

            $orden->examenes()->sync($cantidadesPivot);

            $totalRemisiones = 0;
            $cantidadRemisiones = 0;
            foreach ($orden->remisiones()->get() as $remision) {
                $datos = $remisionesEnviadas->get($remision->id);
                if (! $datos || ! empty($datos['eliminar'])) {
                    $remision->delete();
                    continue;
                }

                $destinoSede = ! empty($datos['destino_sede_id'])
                    ? Sede::findOrFail($datos['destino_sede_id'])
                    : null;
                $destino = $destinoSede?->nombre ?? trim($datos['destino'] ?? '');
                if ($destino === '') {
                    throw ValidationException::withMessages([
                        "remisiones.{$remision->id}.destino" => 'Indique el profesional o la IPS de destino.',
                    ]);
                }

                $remision->update([
                    'destino_sede_id' => $destinoSede?->id,
                    'destino' => $destino,
                ]);
                $totalRemisiones += (float) $remision->valor;
                $cantidadRemisiones++;
            }

            foreach ($remisionesNuevas as $datos) {
                $destinoSede = ! empty($datos['destino_sede_id'])
                    ? Sede::findOrFail($datos['destino_sede_id'])
                    : null;
                $destino = $destinoSede?->nombre ?? trim($datos['destino'] ?? '');
                if ($destino === '') {
                    throw ValidationException::withMessages([
                        'nuevas_remisiones' => 'Indique el profesional o la IPS de destino de cada remisión.',
                    ]);
                }

                $orden->remisiones()->create([
                    'cup' => $datos['cup'],
                    'nombre' => $datos['nombre'],
                    'valor' => $datos['valor'],
                    'destino_sede_id' => $destinoSede?->id,
                    'destino' => $destino,
                ]);
                $totalRemisiones += (float) $datos['valor'];
                $cantidadRemisiones++;
            }

            $totalExamenes = $cantidades->sum(
                fn ($cantidad, $examenId) => (float) $examenes->get($examenId)?->valor * $cantidad
            );
            $total = $totalExamenes + $totalRemisiones;
            $abono = (float) ($validated['abono'] ?? 0);

            if ($cantidades->sum() === 0 && $cantidadRemisiones === 0) {
                throw ValidationException::withMessages([
                    'examenes' => 'La orden debe conservar al menos un examen o una remisión.',
                ]);
            }
            if ($abono > $total) {
                throw ValidationException::withMessages([
                    'abono' => 'El abono no puede ser mayor que el nuevo total de la orden.',
                ]);
            }

            $orden->update([
                'observaciones' => $validated['observaciones'] ?? null,
                'abono' => $abono,
                'total' => $total,
            ]);
        });

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
