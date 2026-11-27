<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IpsExterna;
use App\Models\Remision;
use App\Services\PrepararRemision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RemisionesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Remision::with(['valores' => fn ($query) => $query->orderBy('precio')->orderBy('id'), 'valores.ipsExterna'])
                ->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'cup' => ['required', 'string', 'max:20'],
            'nombre' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'destino' => ['required', 'string', 'max:255'],
            'accion' => ['nullable', Rule::in(['actualizar', 'conservar'])],
        ]);

        return DB::transaction(function () use ($datos) {
            $remision = Remision::firstOrCreate(['cup' => $datos['cup']], ['nombre' => $datos['nombre']]);
            $creada = $remision->wasRecentlyCreated;
            $remision = Remision::whereKey($remision->id)->lockForUpdate()->firstOrFail();
            $accion = $datos['accion'] ?? null;

            $tarifas = $remision->valores()->with('ipsExterna')->orderBy('precio')->orderBy('id')->get();
            $tarifaAnterior = $tarifas->first(fn ($tarifa) => mb_strtolower($tarifa->ipsExterna->nombre) === mb_strtolower($datos['destino']))
                ?? $tarifas->first();

            if (! $creada && $accion === null) {
                return response()->json([
                    'message' => 'Este CUP ya existe. Elige actualizar sus datos o conservar la información anterior.',
                    'data' => $remision->setRelation('valores', $tarifas),
                    'seleccion' => $tarifaAnterior ? PrepararRemision::paraOrden(['valor_remision_id' => $tarifaAnterior->id]) : null,
                ], 409);
            }

            if ($accion === 'conservar') {
                if (! $tarifaAnterior) {
                    throw ValidationException::withMessages([
                        'accion' => 'No hay una tarifa anterior para conservar. Actualiza los datos del CUP.',
                    ]);
                }
                $tarifa = $tarifaAnterior;
            } else {
                $remision->update(['nombre' => $datos['nombre']]);
                $ips = IpsExterna::whereRaw('LOWER(nombre) = ?', [mb_strtolower($datos['destino'])])->first()
                    ?? IpsExterna::firstOrCreate(['nombre' => $datos['destino']]);
                $tarifa = $remision->valores()->updateOrCreate(
                    ['ips_externa_id' => $ips->id],
                    ['precio' => $datos['valor']],
                );
            }

            return response()->json([
                'data' => $remision->load(['valores.ipsExterna']),
                'seleccion' => PrepararRemision::paraOrden(['valor_remision_id' => $tarifa->id]),
            ], $creada ? 201 : 200);
        }, 3);
    }
}
