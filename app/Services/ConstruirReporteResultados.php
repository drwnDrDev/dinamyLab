<?php

namespace App\Services;

use App\Estado;
use App\Models\Orden;
use App\Models\Procedimiento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConstruirReporteResultados
{
    public function paraOrden(Orden $orden): array
    {
        $orden->loadMissing([
            'paciente.tipo_documento',
            'paciente.sexoCatalogo',
            'sede.empresa',
            'remisiones.destinoSede',
        ]);

        $procedimientos = Procedimiento::query()
            ->where('orden_id', $orden->id)
            ->whereIn('estado', [Estado::TERMINADO->value, Estado::ENTREGADO->value])
            ->whereHas('resultado')
            ->with(['examen', 'empleado.user'])
            ->orderBy('id')
            ->get();

        $sede = $orden->sede;
        $empresa = $sede->empresa;
        $paciente = $orden->paciente;
        $direccion = $sede->direccion()->first();
        $correo = $sede->emails()->first();
        $telefono = $sede->telefonos()->pluck('numero')->all();

        $examenes = $procedimientos->map(function (Procedimiento $procedimiento) use ($orden): array {
            $parametros = EscogerReferencia::obtenerResultados($procedimiento);

            if (isset($parametros['info'])) {
                throw new \UnexpectedValueException(
                    "El procedimiento {$procedimiento->id} no devolvió resultados para el reporte."
                );
            }

            $grupos = collect($parametros)
                ->groupBy(fn (array $parametro) => filled($parametro['grupo'] ?? null) ? $parametro['grupo'] : '')
                ->map(fn ($items, string $nombre) => [
                    'nombre' => $nombre !== '' ? $nombre : null,
                    'parametros' => $items->map(fn (array $parametro) => [
                        'nombre' => $parametro['nombre'],
                        'metodo' => $parametro['metodo'] ?? null,
                        'resultado' => $parametro['resultado'] ?? null,
                        'unidades' => $parametro['unidades'] ?? null,
                        'referencia' => $parametro['referencia'] ?? null,
                        'fuera_de_rango' => $this->fueraDeRango($parametro),
                        'tipo' => $this->esTexto($parametro) ? 'texto' : 'valor',
                    ])->all(),
                ])
                ->values()
                ->all();

            $empleado = $procedimiento->empleado;

            return [
                'nombre' => $procedimiento->examen->nombre,
                'orden_numero' => $orden->numero,
                'fecha_validacion' => $procedimiento->updated_at?->format('d-m-Y H:i') ?? '',
                'bacteriologo' => [
                    'nombre' => $empleado?->user?->name ?? 'Profesional no registrado',
                    'documento' => $empleado?->numero_documento ?? '',
                    'firma_src' => $this->imagenComoDataUri($empleado?->firma),
                ],
                'grupos' => $grupos,
            ];
        })->all();

        return [
            'laboratorio' => [
                'nombre_comercial' => $empresa->nombre_comercial ?: $empresa->razon_social,
                'sede' => $sede->nombre,
                'nit' => $empresa->nit,
                'telefonos' => $telefono,
                'direccion' => $direccion?->direccion ?? '',
                'email' => $correo?->email,
                'logo_src' => $this->imagenComoDataUri($sede->logo),
            ],
            'paciente' => [
                'nombre' => $paciente->nombreCompleto(),
                'documento' => trim(($paciente->tipo_documento?->cod_rips ?? '').' '.$paciente->numero_documento),
                'sexo' => $paciente->sexo_nombre,
                'edad' => $paciente->edad($orden->created_at) ?? 'No especificada',
                'fecha_nacimiento' => $paciente->fecha_nacimiento?->format('d-m-Y') ?? 'No especificada',
            ],
            'orden' => [
                'numero' => $orden->numero,
                'fecha_atencion' => $orden->created_at?->format('d-m-Y') ?? '',
            ],
            'fecha_emision' => now()->format('d-m-Y H:i'),
            'total_examenes' => count($examenes),
            'examenes' => $examenes,
            'remisiones' => $orden->remisiones->map(fn ($remision) => [
                'cup' => $remision->cup,
                'nombre' => $remision->nombre,
                'destino' => $remision->destinoSede?->nombre ?? $remision->destino,
            ])->all(),
        ];
    }

    private function fueraDeRango(array $parametro): bool
    {
        return ! $this->esTexto($parametro) && ($parametro['es_normal'] ?? true) === false;
    }

    private function esTexto(array $parametro): bool
    {
        return in_array(Str::lower((string) ($parametro['tipo_dato'] ?? '')), ['texto', 'text', 'string'], true);
    }

    private function imagenComoDataUri(?string $ruta): ?string
    {
        if (! filled($ruta)) {
            return null;
        }

        $ruta = str_replace('\\', '/', $ruta);

        if (Str::startsWith($ruta, '/') || in_array('..', explode('/', $ruta), true)) {
            return null;
        }

        $disco = Storage::disk('public');

        if (! $disco->exists($ruta)) {
            return null;
        }

        $contenido = $disco->get($ruta);
        $imagen = getimagesizefromstring($contenido);
        $mime = $imagen['mime'] ?? null;

        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($contenido);
    }
}
