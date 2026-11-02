<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Examen;
use App\Models\Procedimiento;
use App\Services\EscogerReferencia;
use App\Services\GuardarResultadosLote;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'busqueda' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F,A',
        ]);
        $busqueda = trim($filters['busqueda'] ?? '');
        $examenes = Examen::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('nombre_alternativo', 'like', "%{$busqueda}%")
                        ->orWhere('cup', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->when($filters['sexo'] ?? null, fn ($query, $sexo) => $query->where('sexo_aplicable', $sexo))
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return view('examenes.index', compact('examenes'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Examen $examen)
    {

        return view('examenes.show', compact('examen'));
    }

    /**
     * Show batch processing view for exam
     */
    public function lote(Request $request, Examen $examen)
    {
        $sede = $request->session()->get('sede');
        if (! $sede) {
            return redirect()->route('examenes')->with('error', 'Seleccione una sede para consultar las muestras pendientes.');
        }

        $empleado = $request->user()?->empleado;
        if (! $empleado || ! $empleado->sedes()->whereKey($sede->id)->exists()) {
            abort(403, 'No tiene acceso a la sede seleccionada.');
        }

        if (! $examen->parametros()->exists()) {
            return redirect()->route('examenes.show', $examen)->with('warning', 'Este examen no tiene parámetros configurados.');
        }

        $procedimientos = Procedimiento::query()
            ->where('examen_id', $examen->id)
            ->where('sede_id', $sede->id)
            ->where('estado', Estado::PROCESO->value)
            ->whereDoesntHave('resultado')
            ->with(['orden.paciente.tipo_documento'])
            ->orderBy('fecha')
            ->paginate(25)
            ->withQueryString();

        $parametrosExamen = $examen->parametros()
            ->with(['valoresReferencia', 'opciones'])
            ->orderBy('posicion')
            ->get();

        $muestras = $procedimientos->getCollection()->map(function (Procedimiento $procedimiento) use ($parametrosExamen) {
            $paciente = $procedimiento->orden->paciente;
            $datosDemograficos = EscogerReferencia::datosDemograficos($paciente, Carbon::parse($procedimiento->fecha));
            $parametros = $parametrosExamen->map(function ($parametro) use ($datosDemograficos) {
                $referencia = EscogerReferencia::estableceReferencia($datosDemograficos, $parametro);

                return [
                    'id' => $parametro->id,
                    'nombre' => $parametro->nombre,
                    'grupo' => $parametro->grupo,
                    'tipo_dato' => $parametro->tipo_dato,
                    'default' => $parametro->default,
                    'unidades' => $parametro->unidades,
                    'referencia' => $referencia?->salida,
                    'opciones' => $parametro->opciones->pluck('valor')->all(),
                ];
            })->values();

            return [
                'id' => $procedimiento->id,
                'orden' => $procedimiento->orden->numero,
                'paciente' => $paciente->nombreCompleto(),
                'documento' => ($paciente->tipo_documento?->cod_rips ?? '').$paciente->numero_documento,
                'fecha' => $procedimiento->fecha?->format('d/m/Y H:i'),
                'parametros' => $parametros,
            ];
        })->values();

        return view('examenes.lote', compact('examen', 'procedimientos', 'muestras'));
    }

    public function guardarLote(Request $request, Examen $examen, GuardarResultadosLote $guardarResultadosLote)
    {
        $validated = $request->validate([
            'procedimientos' => ['required', 'array', 'min:1', 'max:25'],
            'procedimientos.*.id' => ['required', 'integer', 'distinct'],
            'procedimientos.*.resultados' => ['required', 'array'],
            'procedimientos.*.resultados.*' => ['nullable', 'string', 'max:255'],
        ]);

        $sede = $request->session()->get('sede');
        $empleado = $request->user()?->empleado;
        if (! $sede || ! $empleado || ! $empleado->sedes()->whereKey($sede->id)->exists()) {
            abort(403, 'No tiene una sede válida seleccionada para registrar resultados.');
        }

        $cantidad = $guardarResultadosLote->guardar(
            $examen,
            (int) $sede->id,
            (int) $empleado->id,
            $validated['procedimientos']
        );

        if ($request->expectsJson()) {
            $mensaje = "Resultados guardados para {$cantidad} muestras.";
            $request->session()->flash('success', $mensaje);

            return response()->json([
                'message' => $mensaje,
                'cantidad' => $cantidad,
                'redirect' => route('examenes.lote', $examen),
            ]);
        }

        return redirect()->route('examenes.lote', $examen)
            ->with('success', "Resultados guardados para {$cantidad} muestras.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Examen $examen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Examen $examen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Examen $examen)
    {
        //
    }
}
