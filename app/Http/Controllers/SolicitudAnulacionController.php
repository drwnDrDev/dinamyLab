<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Procedimiento;
use App\Models\SolicitudAnulacion;
use App\Services\ControlSede;
use App\Services\RegistrarActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SolicitudAnulacionController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate(['estado' => ['nullable', Rule::in(['pendiente', 'aprobada', 'rechazada', 'todas'])]]);
        $estado = $filtros['estado'] ?? 'pendiente';
        $esAdmin = $request->user()->hasRole('admin');
        $sede = $esAdmin ? null : ControlSede::autorizarSedeActiva($request);
        $consulta = SolicitudAnulacion::query()
            ->when(! $esAdmin, fn ($query) => $query->where('solicitante_id', $request->user()->id)->where('sede_id', $sede->id));
        $pendientes = (clone $consulta)->where('estado', 'pendiente')->count();
        $solicitudes = $consulta->with(['procedimiento.examen', 'procedimiento.orden.paciente', 'solicitante', 'resueltaPor', 'sede'])
            ->when($estado !== 'todas', fn ($query) => $query->where('estado', $estado))
            ->latest()->orderByDesc('id')->paginate(25)->withQueryString();

        return view('anulaciones.index', compact('solicitudes', 'estado', 'pendientes', 'esAdmin'));
    }

    public function create(Request $request, Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento($request, $procedimiento);
        $procedimiento->load(['examen', 'orden.paciente', 'sede']);
        $solicitudes = $procedimiento->solicitudesAnulacion()->with(['solicitante', 'resueltaPor'])->latest()->orderByDesc('id')->get();
        $pendiente = $solicitudes->firstWhere('estado', 'pendiente');

        return view('anulaciones.create', compact('procedimiento', 'solicitudes', 'pendiente'));
    }

    public function store(Request $request, Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento($request, $procedimiento);
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:1000']]);

        DB::transaction(function () use ($request, $procedimiento, $datos) {
            $procedimiento = Procedimiento::whereKey($procedimiento->id)->lockForUpdate()->firstOrFail();
            ControlSede::autorizarProcedimiento($request, $procedimiento);
            if (! $procedimiento->puedeSolicitarAnulacion()) {
                throw ValidationException::withMessages(['motivo' => 'Solo se puede solicitar la anulación de un examen pendiente, sin terminar ni exportar.']);
            }
            if ($procedimiento->solicitudAnulacionPendiente()->exists()) {
                throw ValidationException::withMessages(['motivo' => 'Este examen ya tiene una solicitud de anulación pendiente.']);
            }
            $solicitud = $procedimiento->solicitudesAnulacion()->create([
                'sede_id' => $procedimiento->sede_id,
                'solicitante_id' => $request->user()->id,
                'motivo' => $datos['motivo'],
                'estado' => 'pendiente',
            ]);
            RegistrarActividad::registrar('procedimiento.anulacion_solicitada', $procedimiento, ['solicitud_id' => $solicitud->id]);
        }, 3);

        return redirect()->route('anulaciones.create', $procedimiento)
            ->with('success', 'Solicitud enviada a administración. El examen conserva su estado mientras se revisa.');
    }

    public function resolver(Request $request, SolicitudAnulacion $solicitud)
    {
        $datos = $request->validate([
            'decision' => ['required', Rule::in(['aprobar', 'rechazar'])],
            'respuesta' => ['required_if:decision,rechazar', 'nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $solicitud, $datos) {
            $procedimiento = Procedimiento::withTrashed()->whereKey($solicitud->procedimiento_id)->lockForUpdate()->firstOrFail();
            $solicitud = SolicitudAnulacion::whereKey($solicitud->id)->lockForUpdate()->firstOrFail();
            if ($solicitud->estado !== 'pendiente') {
                throw ValidationException::withMessages(['decision' => 'Esta solicitud ya fue resuelta.']);
            }

            $aprobar = $datos['decision'] === 'aprobar';
            if ($aprobar) {
                if (! $procedimiento->puedeSolicitarAnulacion()) {
                    throw ValidationException::withMessages(['decision' => 'El examen ya cambió de estado, fue eliminado o exportado. Revise la solicitud antes de resolverla.']);
                }
                $procedimiento->update(['estado' => Estado::ANULADO->value]);
                $procedimiento->observaciones()->create([
                    'observacion' => "Anulación aprobada por administración. Solicitud #{$solicitud->id}.",
                ]);
            }

            $solicitud->update([
                'estado' => $aprobar ? 'aprobada' : 'rechazada',
                'resuelta_por_id' => $request->user()->id,
                'respuesta' => $datos['respuesta'] ?? null,
                'resuelta_at' => now(),
            ]);
            RegistrarActividad::registrar($aprobar ? 'procedimiento.anulacion_aprobada' : 'procedimiento.anulacion_rechazada', $procedimiento, [
                'solicitud_id' => $solicitud->id,
            ]);
        }, 3);

        return redirect()->route('anulaciones.index')->with('success', $datos['decision'] === 'aprobar'
            ? 'Solicitud aprobada. El examen fue anulado.'
            : 'Solicitud rechazada. El examen conserva su estado.');
    }
}
