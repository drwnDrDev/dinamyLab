<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Orden;
use App\Models\Examen;
use App\Models\Persona;
use App\TipoDocumento;
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
        $ordenes = Orden::orderBy('created_at', 'desc')->get();
        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $tipos_documento = collect(TipoDocumento::cases())
            ->mapWithKeys(fn($tipo) => [$tipo->value => $tipo->nombre()]);
        $examenes = Examen::all();

        $orden_numero = Orden::max('numero') + 1;
        return view('ordenes.create', compact('tipos_documento', 'examenes', 'orden_numero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   // 1. Validación inicial de la solicitud
        $request->validate([
            'paciente_id' => 'required|exists:personas,id',
            'acompaniante_id' => 'nullable|exists:personas,id',
            'numero_orden' => 'required|string|max:20|unique:ordenes_medicas,numero',
            'examenes' => 'array', // Asegura que 'examenes' es un array
        ]);

        // 2. Obtener la instancia del Paciente
        // Necesitamos el modelo del paciente para la validación de género.
        $paciente = Persona::findOrFail($request->input('paciente_id'));

        // 3. Filtrar y validar los exámenes seleccionados
        // Elimina los exámenes con cantidad nula o cero.
        $examenesSolicitados = array_filter(
            $request->input('examenes', []),
            fn ($cantidad) => !is_null($cantidad) && $cantidad != 0
        );

        // Si no se seleccionó ningún examen válido, redirige con un error.
        if (empty($examenesSolicitados)) {
            return redirect()->back()->withErrors(['examenes' => 'Debe seleccionar al menos un examen.'])->withInput();
        }

        // 4. Cargar los detalles completos de los Exámenes desde la base de datos
        // Usamos keyBy('id') para un acceso rápido a cada examen por su ID.
        $examenesData = Examen::whereIn('id', array_keys($examenesSolicitados))
            ->get()
            ->keyBy('id');

        // 5. --- VALIDACIÓN DE GÉNERO PARA CADA EXAMEN CON POLICIES ---
        foreach ($examenesSolicitados as $examenId => $cantidad) {
            $examen = $examenesData->get($examenId);

            // Asegurar que el examen realmente existe en los datos cargados.
            if (!$examen) {
                return redirect()->back()->withErrors(['examenes' => "El examen con ID {$examenId} no se pudo encontrar."])->withInput();
            }

            try {
                // Realizar la autorización utilizando la Policy.
                // Auth::user() es el empleado/doctor actualmente autenticado.
                // Los argumentos adicionales ($paciente, $examen) se pasan al método performOnPatient de la Policy.
                // Si la autorización falla, se lanzará una AuthorizationException.
                Auth::user()->can(ExaminationPolicy::class . ':performOnPatient', [$paciente, $examen]);

                // Si también tienes políticas para procedimientos y los procedimientos son una entidad diferente al examen
                // y se basan en la misma lógica de restricción:
                // Auth::user()->can(ProcedurePolicy::class . ':performOnPatient', [$paciente, $procedimiento_asociado]);

            } catch (AuthorizationException $e) {
                // Capturar la excepción de autorización y redirigir con un mensaje de error.
                return redirect()->back()->withErrors([
                    'examenes' => 'Restricción de género: No se puede solicitar el examen "' . $examen->nombre . '" para el paciente. ' . $e->getMessage()
                ])->withInput(); // Mantener los datos del formulario para una mejor UX
            }
        }
        // --- FIN VALIDACIÓN DE GÉNERO ---

        // 6. Envolver la creación de la orden y procedimientos en una transacción de base de datos
        // Esto asegura que todas las operaciones se completen con éxito o ninguna lo haga (atomicidad).
        DB::transaction(function () use ($request, $paciente, $examenesSolicitados, $examenesData) {
            // 7. Calcular el total de la orden
            $total = $examenesData->sum(function ($examen) use ($examenesSolicitados) {
                return $examen->valor * $examenesSolicitados[$examen->id];
            });

            // 8. Determinar el abono (si se paga todo o una parte)
            $abono = $request->input('pago') === "on" ? $total : $request->input('abono', 0);

            // 9. Crear la Orden Médica
            $orden = Orden::create([
                'numero' => $request->input('numero_orden'),
                'paciente_id' => $paciente->id, // Usar el ID del paciente cargado
                'acompaniante_id' => $request->input('acompaniante_id'),
                'descripcion' => $request->input('observaciones'),
                'abono' => $abono,
                'total' => $total,
            ]);

            // 10. Preparar los datos para los Procedimientos (que se asocian a la Orden)
            $procedimientosParaInsertar = [];
            foreach ($examenesSolicitados as $examenId => $cantidad) {
                for ($i = 0; $i < $cantidad; $i++) {
                    $procedimientosParaInsertar[] = [
                        'orden_id' => $orden->id, // ID de la orden recién creada
                        'empleado_id' => Auth::user()->id, // ID del empleado/doctor autenticado
                        'examen_id' => $examenId,
                        'fecha' => now(),
                        'estado' => Estado::PROCESO, // Asumo que Estado::PROCESO es una constante o Enum
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // 11. Insertar los Procedimientos en la base de datos
            Procedimiento::insert($procedimientosParaInsertar);
        }); // Fin de la transacción

        // 12. Redirigir con mensaje de éxito si todo fue bien
        return redirect()->route('ordenes')->with('success', 'Orden médica creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        $orden->load(['paciente', 'acompaniante']);

        $procedimientos = $orden->procedimientos;
        return view('ordenes.show', compact('orden', 'procedimientos'));
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
