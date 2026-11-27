<?php

namespace App\Services;

use App\Cargo;
use App\Estado;
use App\Models\Orden;
use App\Models\Procedimiento;
use App\Models\Sede;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConstruirReporteResultados
{
    public function __construct(
        private readonly FirmaReporte $firmaReporte,
        private readonly CodigoVerificacionReporte $codigoVerificacion,
    ) {}

    public function paraOrden(Orden $orden): array
    {
        $orden->loadMissing([
            'paciente.tipo_documento',
            'paciente.sexoCatalogo',
            'sede.empresa',
        ]);

        $procedimientos = Procedimiento::query()
            ->where('orden_id', $orden->id)
            ->whereIn('estado', [Estado::TERMINADO->value, Estado::ENTREGADO->value])
            ->whereHas('resultado')
            ->with(['examen', 'empleado.user'])
            ->orderBy('id')
            ->get();

        return $this->paraProcedimientos($procedimientos, $orden);
    }

    public function paraProcedimientos(Collection $procedimientos, ?Orden $orden = null): array
    {
        $ordenReporte = $orden;

        $procedimientos = $procedimientos->values();

        $procedimientos->each(fn (Procedimiento $procedimiento) => $procedimiento->loadMissing([
            'orden.paciente.tipo_documento',
            'orden.paciente.sexoCatalogo',
            'orden.sede.empresa',
            'sede.empresa',
            'examen',
            'empleado.user',
        ]));

        $primerProcedimiento = $procedimientos->first();
        $orden ??= $primerProcedimiento?->orden;

        if (! $orden && ! $primerProcedimiento) {
            throw new \InvalidArgumentException('Se requiere una orden o al menos un procedimiento para construir el reporte.');
        }

        $ordenPaciente = $orden ?? $primerProcedimiento->orden;
        $persona = $ordenPaciente->paciente;
        $sede = $primerProcedimiento?->sede ?? $ordenPaciente->sede;

        foreach ($procedimientos as $procedimiento) {
            if ((int) $procedimiento->orden->paciente_id !== (int) $persona->id) {
                throw new \InvalidArgumentException('Los procedimientos del reporte deben pertenecer al mismo paciente.');
            }
        }

        $persona->loadMissing(['tipo_documento', 'sexoCatalogo']);
        $sedesParaReporte = $procedimientos->pluck('sede')->filter()->unique('id');
        if ($sedesParaReporte->isEmpty()) {
            $sedesParaReporte = collect([$sede]);
        }
        $laboratoriosPorSede = $sedesParaReporte->mapWithKeys(
            fn (Sede $sedeReporte) => [$sedeReporte->id => $this->datosSede($sedeReporte)]
        );
        $laboratorioPrincipal = $laboratoriosPorSede->first();
        $laboratorioEncabezado = $laboratoriosPorSede->count() > 1
            ? [
                ...$laboratorioPrincipal,
                'razon_social' => 'Reporte consolidado',
                'nombre_tipo' => null,
                'nombre_titular' => 'Reporte consolidado',
                'sede' => 'Varias sedes',
                'reps' => null,
                'registro_medico' => '',
                'telefonos' => [],
                'direccion' => '',
                'email' => null,
                'logo_src' => null,
            ]
            : $laboratorioPrincipal;

        $examenes = $procedimientos->map(function (Procedimiento $procedimiento) use ($laboratoriosPorSede): array {
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
                'procedimiento_id' => $procedimiento->id,
                'orden_id' => $procedimiento->orden->id,
                'laboratorio' => $laboratoriosPorSede->get($procedimiento->sede_id),
                'nombre' => $procedimiento->examen->nombre,
                'orden_numero' => $procedimiento->orden->numero,
                'fecha_atencion' => $procedimiento->orden->created_at?->format('d-m-Y') ?? '',
                'fecha_validacion' => $procedimiento->updated_at?->format('d-m-Y H:i') ?? '',
                'bacteriologo' => [
                    'nombre' => $empleado?->user?->name ?? 'Profesional no registrado',
                    'documento' => $empleado?->numero_documento ?? '',
                    'firma_src' => null,
                ],
                'grupos' => $grupos,
            ];
        })->values()->all();

        $reporte = [
            'laboratorio' => $laboratorioEncabezado,
            'paciente' => [
                'nombre' => $persona->nombreCompleto(),
                'documento' => trim(($persona->tipo_documento?->cod_rips ?? '').' '.$persona->numero_documento),
                'sexo' => $persona->sexo_nombre,
                'edad' => $persona->edad($ordenPaciente->created_at) ?? 'No especificada',
                'fecha_nacimiento' => $persona->fecha_nacimiento?->format('d-m-Y') ?? 'No especificada',
            ],
            'orden' => $ordenReporte ? [
                'numero' => $ordenReporte->numero,
                'fecha_atencion' => $ordenReporte->created_at?->format('d-m-Y') ?? '',
            ] : null,
            'fecha_emision' => now()->format('d-m-Y H:i'),
            'codigo_verificacion' => null,
            'total_examenes' => count($examenes),
            'varias_sedes' => $laboratoriosPorSede->count() > 1,
            'examenes' => $examenes,
        ];

        $reporte['codigo_verificacion'] = $this->codigoVerificacion->generar($reporte);

        $empleados = $procedimientos->pluck('empleado_id')
            ->map(fn ($id) => $id === null ? null : (int) $id)
            ->all();
        $total = count($empleados);
        $marca = ($ordenReporte ? 'ORDEN '.$ordenReporte->numero : 'HISTORIA')
            .' · '.$reporte['paciente']['documento']
            .' · '.$reporte['codigo_verificacion'];

        /*
         * El reporte agrupa los exámenes por orden al renderizar, así que el cierre de grupo se
         * calcula dentro de cada orden y no sobre la lista plana: una historia con órdenes
         * intercaladas no debe dejar una sección entera sin firma.
         */
        foreach ($reporte['examenes'] as &$examen) {
            $examen['cierra_grupo'] = false;
        }
        unset($examen);

        $indicesPorOrden = [];
        foreach ($reporte['examenes'] as $indice => $examen) {
            $indicesPorOrden[$examen['orden_id'] ?? 'sin-orden'][] = $indice;
        }

        foreach ($indicesPorOrden as $indices) {
            $ultimo = count($indices) - 1;

            foreach ($indices as $posicion => $indice) {
                $reporte['examenes'][$indice]['cierra_grupo'] = $posicion === $ultimo
                    || $empleados[$indice] !== $empleados[$indices[$posicion + 1]];
            }
        }

        $distintos = collect($empleados)->unique()->values();

        if ($total === 0) {
            $reporte['firma'] = null;
        } elseif ($distintos->count() === 1) {
            // Caso normal: una sola firma en el pie del documento, una por hoja del PDF.
            $empleado = $procedimientos->first()?->empleado;

            $reporte['firma'] = [
                'src' => $this->firmaReporte->componer($empleado?->firma, $marca),
                'nombre' => $empleado?->user?->name ?? 'Profesional no registrado',
                'documento' => $empleado?->numero_documento ?? '',
            ];
        } else {
            // Varios profesionales: se firma al cierre de cada grupo consecutivo.
            $reporte['firma'] = null;

            foreach ($reporte['examenes'] as $indice => &$examen) {
                if (! $examen['cierra_grupo']) {
                    continue;
                }

                $examen['bacteriologo']['firma_src'] = $this->firmaReporte->componer(
                    $procedimientos[$indice]?->empleado?->firma,
                    $marca,
                );
            }
            unset($examen);
        }

        return $reporte;
    }

    private function datosSede(Sede $sede): array
    {
        $sede->loadMissing('empresa');
        $empresa = $sede->empresa;

        [$nombreTipo, $nombreTitular] = $empresa->nombreEnLineas();

        return [
            'razon_social' => $empresa->razon_social,
            // Encabezado en dos líneas: "Laboratorio clínico" / "Dra. Nombre Apellido"
            'nombre_tipo' => $nombreTipo,
            'nombre_titular' => $nombreTitular,
            'sede' => $sede->nombre,
            'reps' => $sede->codigo_prestador,
            'registro_medico' => $empresa->empleados()
                ->where('cargo', Cargo::PRESTADOR->value)
                ->orderBy('id')
                ->value('numero_documento')
                // Respaldo: el cargo es texto libre en muchas instancias; se usa el rol de acceso.
                ?? $empresa->empleados()
                    ->whereHas('user', fn ($usuario) => $usuario->role('prestador'))
                    ->orderBy('id')
                    ->value('numero_documento'),
            'telefonos' => $sede->telefonos()->pluck('numero')->all(),
            'direccion' => $sede->direccion()->first()?->direccion ?? '',
            'email' => $sede->emails()->first()?->email,
            'logo_src' => $this->imagenComoDataUri($sede->logo, 'logos'),
        ];
    }

    private function fueraDeRango(array $parametro): bool
    {
        return ! $this->esTexto($parametro) && ($parametro['es_normal'] ?? true) === false;
    }

    private function esTexto(array $parametro): bool
    {
        return Str::lower((string) ($parametro['tipo_dato'] ?? '')) === 'textarea'
            || Str::lower(trim((string) ($parametro['nombre'] ?? ''))) === 'observaciones';
    }

    private function imagenComoDataUri(?string $ruta, string $carpeta): ?string
    {
        if (! filled($ruta)) {
            return null;
        }

        $ruta = str_replace('\\', '/', $ruta);

        if (Str::startsWith($ruta, '/') || in_array('..', explode('/', $ruta), true)) {
            return null;
        }

        $disco = Storage::disk($carpeta === 'firmas' ? 'local' : 'public');
        if ($carpeta === 'logos' && ! Str::startsWith($ruta, 'logos/')) {
            $ruta = 'logos/'.$ruta;
        }
        // Las firmas se buscan exclusivamente en storage/app/private.
        if (! $disco->exists($ruta)) {
            if ($carpeta !== 'firmas' || ! $disco->exists('firmas/'.$ruta)) {
                return null;
            }
            $ruta = 'firmas/'.$ruta;
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
