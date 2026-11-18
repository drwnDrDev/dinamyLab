<?php

namespace App\Http\Controllers;

use App\Models\EventoActividad;
use App\Models\Procedimiento;
use App\Services\ControlSede;
use App\Services\RegistrarActividad;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'evento' => ['nullable', Rule::in(array_keys(EventoActividad::EVENTOS))],
            'usuario_id' => ['nullable', 'integer', 'min:1'],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => array_filter(['nullable', 'date_format:Y-m-d', $request->filled('desde') ? 'after_or_equal:desde' : null]),
        ]);
        $zona = 'America/Bogota';
        $eventos = EventoActividad::query()
            ->when($filtros['evento'] ?? null, fn ($q, $v) => $q->where('evento', $v))
            ->when($filtros['usuario_id'] ?? null, fn ($q, $v) => $q->where('usuario_id', $v))
            ->when($filtros['desde'] ?? null, fn ($q, $v) => $q->where('ocurrio_en', '>=', CarbonImmutable::parse($v, $zona)->startOfDay()->utc()))
            ->when($filtros['hasta'] ?? null, fn ($q, $v) => $q->where('ocurrio_en', '<', CarbonImmutable::parse($v, $zona)->addDay()->startOfDay()->utc()))
            ->orderByDesc('ocurrio_en')->orderByDesc('id')
            ->paginate(50)->withQueryString();
        // Incluye usuarios eliminados: los nombres se conservan en cada evento.
        $usuarios = EventoActividad::query()->select('usuario_id')->selectRaw('MAX(usuario_nombre) as usuario_nombre')
            ->whereNotNull('usuario_id')->groupBy('usuario_id')->orderBy('usuario_nombre')->get();

        return view('actividad.index', compact('eventos', 'usuarios', 'zona'));
    }

    public function impresion(Request $request)
    {
        $validated = $request->validate(['token' => ['required', 'string', 'max:30000']]);
        try {
            $datos = json_decode(Crypt::decryptString($validated['token']), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException $e) {
            abort(422, 'El reporte de impresión no es válido.');
        }
        abort_unless(($datos['usuario_id'] ?? null) === $request->user()->id
            && ($datos['expira'] ?? 0) >= now()->timestamp, 403);
        $ids = $datos['procedimientos'] ?? [];
        abort_if(empty($ids), 422);
        $procedimientos = Procedimiento::whereKey($ids)->whereHas('resultado')->get();
        abort_unless($procedimientos->count() === count($ids), 403);
        foreach ($procedimientos as $procedimiento) {
            ControlSede::autorizarProcedimiento($request, $procedimiento);
        }
        RegistrarActividad::registrar('resultados.impresion_solicitada', $procedimientos->first(), [
            'procedimientos' => $procedimientos->modelKeys(),
            'cantidad' => $procedimientos->count(),
        ]);

        return response()->noContent();
    }

    public static function tokenImpresion($procedimientos): string
    {
        return Crypt::encryptString(json_encode([
            'usuario_id' => auth()->id(),
            'procedimientos' => $procedimientos->pluck('id')->all(),
            'expira' => now()->addHours(8)->timestamp,
        ], JSON_THROW_ON_ERROR));
    }
}
