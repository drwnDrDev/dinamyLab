<?php


namespace App\Http\Controllers;

use App\Estado;

use Illuminate\Http\Request;
use App\Models\Procedimiento;
use App\Services\ControlSede;
use App\Services\EscogerReferencia;
use App\Services\ActualizarResultados;
use App\Services\ConstruirReporteResultados;
use App\Models\Persona;
use App\Models\Orden;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResultadosController extends Controller
{

    public function index(Request $request)
    {
        $sede = $request->user()->hasRole('admin')
            ? null
            : ControlSede::autorizarSedeActiva($request);
        $filters = $request->validate(['busqueda' => 'nullable|string|max:100']);
        $busqueda = trim($filters['busqueda'] ?? '');
        $resultados = Procedimiento::with(['orden.paciente', 'resultado', 'examen'])
            ->whereHas('resultado')
            ->where('estado', Estado::TERMINADO)
            ->when($sede, fn ($query) => $query->where('sede_id', $sede->id))
            ->where('updated_at', '>=', now()->subDays(2))
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->whereHas('orden', fn ($orden) => $orden->where('numero', 'like', "%{$busqueda}%")
                        ->orWhereHas('paciente', function ($paciente) use ($busqueda) {
                            $paciente->where('numero_documento', 'like', "%{$busqueda}%")
                                ->orWhere('primer_nombre', 'like', "%{$busqueda}%")
                                ->orWhere('segundo_nombre', 'like', "%{$busqueda}%")
                                ->orWhere('primer_apellido', 'like', "%{$busqueda}%")
                                ->orWhere('segundo_apellido', 'like', "%{$busqueda}%");
                        }))
                        ->orWhereHas('examen', fn ($examen) => $examen->where('nombre', 'like', "%{$busqueda}%"));
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(25)
            ->withQueryString();
        return view('resultados.index', compact('resultados'));
    }
    public function show(Procedimiento $procedimiento, ConstruirReporteResultados $construirReporte)
    {
        $this->autorizarSede(request(), $procedimiento);

        $procedimiento->load(['orden.paciente', 'resultado', 'sede']);

        if ($procedimiento->resultado->isEmpty()) {
            $destino = request()->user()->hasRole('prestador')
                ? 'resultados.create'
                : 'procedimientos.show';

            return redirect()->route($destino, $procedimiento)
                ->with('warning', 'Resultados no encontrados para este procedimiento.');
        }

        $reporte = $construirReporte->paraProcedimientos(
            collect([$procedimiento]),
            $procedimiento->orden
        );
        $cambios = $procedimiento->cambiosResultado;

        return view('resultados.reporte', [
            'reporte' => $reporte,
            'enPdf' => false,
            'cambios' => $cambios,
            'urlVolver' => route('resultados.reporte_orden', $procedimiento->orden),
        ]);
    }

    public function reporteOrden(Orden $orden, ConstruirReporteResultados $construirReporte)
    {
        ControlSede::autorizarOrden(request(), $orden);
        $reporte = $construirReporte->paraOrden($orden);

        return view('resultados.reporte', [
            'reporte' => $reporte,
            'enPdf' => false,
            'urlVolver' => route('ordenes.show', $orden),
            'urlPdf' => route('resultados.reporte_pdf', $orden),
        ]);
    }

    public function reporteOrdenPdf(Orden $orden, ConstruirReporteResultados $construirReporte)
    {
        ControlSede::autorizarOrden(request(), $orden);
        $reporte = $construirReporte->paraOrden($orden);

        if ($reporte['total_examenes'] === 0) {
            return redirect()->route('resultados.reporte_orden', $orden)
                ->with('warning', 'Esta orden aún no tiene exámenes terminados con resultados para descargar.');
        }

        $contenidoPdf = Pdf::loadView('resultados.reporte', [
            'reporte' => $reporte,
            'enPdf' => true,
        ])->setPaper('letter')->output();

        Log::info('Reporte de resultados generado.', [
            'usuario_id' => request()->user()?->id,
            'orden_id' => $orden->id,
            'sede_id' => $orden->sede_id,
            'total_examenes' => $reporte['total_examenes'],
        ]);

        $numeroOrden = Str::slug((string) $orden->numero) ?: (string) $orden->id;

        return response($contenidoPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="resultados-orden-'.$numeroOrden.'.pdf"',
        ]);
    }

    public function create(Procedimiento $procedimiento)
    {
        $this->autorizarSede(request(), $procedimiento);

        $parametros = EscogerReferencia::recorrerParametrosExamen($procedimiento->load(['orden.paciente', 'examen.parametros']));
        if (empty($parametros)) {
            return redirect()->route('resultados.show', $procedimiento)
                ->with('warning', 'No hay parámetros para este examen. Por favor, crea los resultados.');
        }
        if ($procedimiento->estado === Estado::TERMINADO->value || $procedimiento->estado === Estado::ENTREGADO->value) {
            return redirect()->route('resultados.show', $procedimiento->id)
                ->with('info', 'Este procedimiento ya ha sido completado.');
        }
        if ($procedimiento->estado === Estado::ANULADO->value) {
            return redirect()->route('resultados.show', $procedimiento)
                ->with('error', 'Este procedimiento ha sido anulado y no puede ser editado.');
        }

        if ($procedimiento->estado === Estado::PENDIENTE->value || $procedimiento->estado === Estado::MUESTRA->value) {
            $procedimiento->estado = Estado::PROCESO->value; // Cambia el estado a 'en proceso'
            $procedimiento->save();
        }

        return view('resultados.create', compact('parametros', 'procedimiento'));
    }
    public function store(Request $request, Procedimiento $procedimiento)
    {
    $this->autorizarSede($request, $procedimiento);

    $procedimiento->fecha = $request->input('fecha_procedimiento', now()); // Actualiza la fecha del procedimiento con la fecha enviada o la fecha actual
    $procedimiento->save();

    // Validar unicidad de (procedimiento_id, parametro_id) antes de guardar
        $input = collect($request->except(['_token', 'submit', 'resultados']));

        // Si viene la estructura 'resultados' (para procesamiento por lotes), usar esa
        if ($request->has('resultados')) {
            $input = collect($request->input('resultados'));
        }

        $duplicates = [];

        foreach ($input->keys() as $paramId) {
            if (is_numeric($paramId) && $procedimiento->resultado()->where('parametro_id', $paramId)->exists()) {
                $duplicates[] = $paramId;
            }
        }

        if (!empty($duplicates)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existen resultados para los parámetros: ' . implode(', ', $duplicates)
                ], 422);
            }
            return redirect()->back()
                ->with('warning', 'Ya existen resultados para los parámetros: ' . implode(', ', $duplicates));
        }

        EscogerReferencia::guardaResultado($input->toArray(), $procedimiento);

        $procedimiento->estado = Estado::TERMINADO; // Cambia el estado del procedimiento a 'terminado'
        $procedimiento->fecha = now(); // Actualiza la fecha del procedimiento
        $procedimiento->empleado_id = auth()->user()->empleado->id; // Asigna el empleado que guarda los resultados
        $procedimiento->save();

        // Si es una petición JSON (AJAX), devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Resultados guardados correctamente.',
                'procedimiento_id' => $procedimiento->id
            ], 200);
        }

        return redirect()->route('resultados.show', $procedimiento)->with('success', 'Resultados guardados correctamente.');
    }
    public function edit(Procedimiento $procedimiento)
    {
        $this->autorizarSede(request(), $procedimiento);

        // Carga el procedimiento con sus relaciones necesarias
        $procedimiento->load(['orden.paciente', 'resultado', 'sede', 'examen.parametros.opciones']);

        // Verifica si el procedimiento ya tiene resultados
        if ($procedimiento->resultado->isEmpty()) {
            return redirect()->route('resultados.create', $procedimiento)
                ->with('warning', 'No hay parámetros para este examen. Por favor, crea los resultados.');
        }

        // Combina parámetros del examen con resultados en una sola colección
        $parametros = collect(EscogerReferencia::obtenerResultados($procedimiento));


        return view('resultados.edit', compact('procedimiento', 'parametros'));
    }
    public function update(Request $request, Procedimiento $procedimiento, ActualizarResultados $actualizarResultados)
    {
        $this->autorizarSede($request, $procedimiento);
        $validated = $request->validate([
            'observacion' => ['required', 'string', 'max:1000'],
            'resultados' => ['required', 'array', 'min:1'],
            'resultados.*' => ['nullable', 'string', 'max:255'],
        ]);

        $cantidadCambios = $actualizarResultados->actualizar(
            $procedimiento,
            (int) ($request->session()->get('sede')?->id ?? $procedimiento->sede_id),
            $validated['resultados'],
            trim($validated['observacion']),
            (int) $request->user()->id,
            $request->user()->name
        );

        $mensaje = $cantidadCambios > 0
            ? "Se guardaron {$cantidadCambios} cambio(s) en los resultados."
            : 'No se detectaron cambios en los resultados.';

        return redirect()->route('resultados.show', $procedimiento)->with('success', $mensaje);
    }

    private function autorizarSede(Request $request, Procedimiento $procedimiento): void
    {
        ControlSede::autorizarProcedimiento($request, $procedimiento);
    }

    public function historia(Persona $persona)
    {
        $esAdmin = request()->user()->hasRole('admin');
        $sede = $esAdmin
            ? request()->session()->get('sede')
            : ControlSede::autorizarSedeActiva(request());
        if (! $sede && ! $esAdmin) {
            return redirect()->route('dashboard')
                ->with('error', 'Selecciona una sede para imprimir el historial clínico.');
        }
        $ordenes = Orden::where('paciente_id', $persona->id)
            ->when($sede, fn ($query) => $query->where('sede_id', $sede->id))
            ->with(['procedimientos' => fn ($query) => $query
                ->whereIn('estado', [Estado::TERMINADO->value, Estado::ENTREGADO->value])
                ->whereHas('resultado')
                ->with(['examen', 'empleado.user'])])
            ->orderBy('updated_at', 'desc')
            ->get();
        return view(
            'resultados.historia',
            compact('persona', 'ordenes')
        );
    }

    public function print_resultado(Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento(request(), $procedimiento);
        $procedimiento->load(['orden.paciente', 'resultado', 'sede', 'examen.parametros.opciones']);
        $parametros = EscogerReferencia::obtenerResultados($procedimiento);

        return view('resultados.print_resultado', compact('procedimiento', 'parametros'));
    }

    public function historia_show(
        Request $request,
        Persona $persona,
        ConstruirReporteResultados $construirReporte
    ) {
        $esAdmin = $request->user()->hasRole('admin');
        $sede = $esAdmin
            ? $request->session()->get('sede')
            : ControlSede::autorizarSedeActiva($request);
        if (! $sede && ! $esAdmin) {
            return redirect()->route('dashboard')
                ->with('error', 'Selecciona una sede para imprimir el historial clínico.');
        }

        $validated = $request->validate([
            'procedimientos' => ['required', 'array', 'min:1'],
            'procedimientos.*' => ['required', 'integer', 'distinct'],
        ]);

        $procedimientos = Procedimiento::when($sede, fn ($query) => $query->where('sede_id', $sede->id))
            ->whereHas('orden', fn ($orden) => $orden->where('paciente_id', $persona->id))
            ->whereIn('id', $validated['procedimientos'])
            ->whereIn('estado', [Estado::TERMINADO->value, Estado::ENTREGADO->value])
            ->whereHas('resultado')
            ->with(['orden', 'examen', 'empleado.user'])
            ->orderBy('id')
            ->get();

        if ($procedimientos->isEmpty()) {
            return redirect()->route('resultados.historia', $persona)
                ->with('warning', 'Selecciona procedimientos terminados con resultados para imprimir.');
        }

        $procedimientos->each(fn (Procedimiento $procedimiento) => ControlSede::autorizarProcedimiento($request, $procedimiento));
        $reporte = $construirReporte->paraProcedimientos($procedimientos);

        return view('resultados.reporte', [
            'reporte' => $reporte,
            'enPdf' => false,
            'tituloReporte' => 'Historia clínica',
            'urlVolver' => route('resultados.historia', $persona),
        ]);
    }
}
