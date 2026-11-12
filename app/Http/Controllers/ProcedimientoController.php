<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Empleado;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Services\ControlSede;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProcedimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sede = $request->user()->hasRole('admin')
            ? $request->session()->get('sede')
            : ControlSede::autorizarSedeActiva($request);
        $filters = $request->validate(['busqueda' => 'nullable|string|max:100']);
        $busqueda = trim($filters['busqueda'] ?? '');
        $procedimientos = Procedimiento::with(['orden.paciente', 'examen'])
            ->when(! $request->user()->hasRole('admin'), fn ($query) => $query->where('sede_id', $sede->id))
            ->where('estado', 'en proceso')
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
            ->orderByDesc('updated_at')
            ->paginate(25)
            ->withQueryString();

        return view('procedimientos.index', compact('procedimientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ordenes =Orden::with(['paciente'])
            ->where('estado', '!=', 'CANCELADA')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('procedimientos.resultados', compact('ordenes'));
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
    public function show(Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento(request(), $procedimiento);

        return view('procedimientos.show', compact('procedimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Procedimiento $procedimiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Procedimiento $procedimiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento(request(), $procedimiento);
        $procedimiento->delete();

        return redirect()->route('procedimientos')->with('success', 'Procedimiento eliminado correctamente.');
    }

    public function resultado()
    {
        $ordenes = Orden::with(['paciente'])
            ->where('estado', '!=', 'CANCELADA')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('procedimientos.resultados', compact('ordenes'));
    }
    /**
     * Display a listing of the examenes.
     */

    public function reportes(Request $request){

        $sede = $request->user()->hasRole('admin')
            ? null
            : ControlSede::autorizarSedeActiva($request);
        $sedes = $sede ? collect([$sede]) : Sede::all();

        $validated = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);
        $startDate = Carbon::parse($validated['fecha_inicio'] ?? now()->startOfMonth())->startOfDay();
        $endDate = Carbon::parse($validated['fecha_fin'] ?? now()->endOfMonth())->endOfDay();
        $procedimientos = Procedimiento::with(['examen'])
            ->where('fecha', '>=', $startDate)
            ->where('fecha', '<=', $endDate)
            ->when($sede, fn ($query) => $query->where('sede_id', $sede->id))
            ->selectRaw('examen_id, COUNT(*) as total_procedimientos')
            ->groupBy('examen_id')
            ->orderByDesc('total_procedimientos')
            ->get();
        return view('procedimientos.rips', compact('procedimientos', 'sedes','startDate', 'endDate'));
    }

    public function examenes()
    {
        $examenes = Examen::with(['procedimientos', 'ordenes'])
            ->orderBy('nombre')
            ->get();
        return view('procedimientos.examenes', compact('examenes'));
    }
    public function observaciones(Request $request, Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento($request, $procedimiento);
        $request->validate([
            'observacion' => 'required|string|max:255',
            'estado' => 'required|in:pendiente de muestra,anulado',
        ]);
        DB::transaction(function () use ($procedimiento, $request) {
            $procedimiento->estado = $request->input('estado');
            $procedimiento->save();
            $procedimiento->observaciones()->create([
                'observacion' => $request->input('observacion'),
            ]);
        });


        return response()->json([
            'message' => 'Observación actualizada correctamente',
            'estado' => $procedimiento->estado,
            'observacion' => $request->input('observacion'),
        ]);
    }

public function json_rips(Request $request)
{
    $validated = $request->validate([
        'sede_id' => ['required', 'integer', 'exists:sedes,id'],
        'fecha_inicio' => ['nullable', 'date'],
        'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
    ]);
    $sede = Sede::findOrFail($validated['sede_id']);
    ControlSede::autorizarEleccion($request, $sede);

    $fechaInicio = Carbon::parse($validated['fecha_inicio'] ?? now()->startOfMonth())->startOfDay();
    $fechaFin = Carbon::parse($validated['fecha_fin'] ?? now()->endOfMonth())->endOfDay();
    $procedimientos = Procedimiento::with([
        'orden.paciente.tipo_documento',
        'sede',
        'examen',
        'servicioHabilitado',
        'diagnosticoPrincipal',
        'diagnosticoRelacionado',
    ])
        ->whereBetween('fecha', [$fechaInicio, $fechaFin])
        ->where('sede_id', $sede->id)
        ->where('estado', 'terminado')
        ->get()
        ->groupBy('orden.paciente_id')
        ->values()
        ->map(function ($usuarios, $index) {
            $paciente = $usuarios->first()->orden->paciente;

            return [
                'tipoDocumentoIdentificacion' => $paciente->tipo_documento->cod_rips,
                'numDocumentoIdentificacion' => $paciente->numero_documento,
                'tipoUsuario' => '04',
                'fechaNacimiento' => $paciente->fecha_nacimiento->format('Y-m-d'),
                'codSexo' => $paciente->sexo_rips,
                'codPaisResidencia' => $paciente->cod_pais_residencia ?? '170',
                'codMunicipioResidencia' => $paciente->cod_municipio_residencia ?? '11001',
                'codZonaTerritorialResidencia' => $paciente->cod_zona_territorial_residencia ?? '01',
                'incapacidad' => $paciente->incapacidad ?? 'NO',
                'codPaisOrigen' => $paciente->cod_pais_origen ?? '170',
                'consecutivo' => $index + 1,
                'servicios' => [
                    'procedimientos' => $usuarios->values()->map(function ($procedimiento, $index) {
                        return [
                            'codPrestador' => $procedimiento->sede->codigo_prestador,
                            'fechaInicioAtencion' => $procedimiento->fecha->format('Y-m-d H:i'),
                            'idMIPRES' => '',
                            'numAutorizacion' => null,
                            'codProcedimiento' => $procedimiento->examen->cup,
                            'viaIngresoServicioSalud' => $procedimiento->codigo_via_ingreso,
                            'modalidadGrupoServicioTecSal' => $procedimiento->codigo_modalidad,
                            'grupoServicios' => $procedimiento->servicioHabilitado->codigo_grupo ?? '03',
                            'codServicio' => $procedimiento->codigo_servicio,
                            'finalidadTecnologiaSalud' => $procedimiento->codigo_finalidad,
                            'tipoDocumentoIdentificacion' => 'CC',
                            'numDocumentoIdentificacion' => '51934571',
                            'codDiagnosticoPrincipal' => $procedimiento->diagnosticoPrincipal?->codigo,
                            'codDiagnosticoRelacionado' => $procedimiento->diagnosticoRelacionado?->codigo,
                            'codComplicacion' => null,
                            'vrServicio' => 0,
                            'conceptoRecaudo' => '05',
                            'valorPagoModerador' => 0,
                            'numFEVPagoModerador' => '',
                            'consecutivo' => $index + 1,
                        ];
                    }),
                ],
            ];
        });

    $json = json_encode([
        'numDocumentoIdObligado' => '51934571',
        'numFactura' => null,
        'tipoNota' => 'RS',
        'numNota' => date('Ym'),
        'usuarios' => $procedimientos,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fileName = 'rips_'.date('YmdHis').'.json';

    return response($json, 200)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
}
}
