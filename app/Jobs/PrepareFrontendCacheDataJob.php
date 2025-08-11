<?php

namespace App\Jobs;

use App\TipoDocumento;
use App\Models\Pais;
use App\Models\Municipio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Opcional, si quieres que sea encolable
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // Para almacenar en caché si es necesario

// Si no necesitas que el job sea encolable y se ejecute en segundo plano,
// puedes quitar `implements ShouldQueue`.
// Para este caso, donde los datos se necesitan para una respuesta API inmediata,
// usualmente no se encola o se despacha de forma síncrona.
class PrepareFrontendCacheDataJob // implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $dataForFrontend;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $tiposDocumentoPaciente = TipoDocumento::where('es_paciente', true)->orderBy('nivel','desc')->get()->toArray();
            $tiposDocumentoPagador = TipoDocumento::where('es_pagador', true)->orderBy('nivel','desc')->get()->toArray();
            $paises = Pais::select('nombre', 'codigo_iso')->orderBy('nivel','desc')->get()->toArray();
            $municipios = Municipio::select('id', 'municipio', 'departamento')->orderBy('nivel','desc')->get()->toArray();
            $eps = \App\Models\Eps::select('id', 'nombre')
                ->where('verificada', true) // Solo EPS verificadas
                ->orderBy('nombre')->get()->toArray();

            $this->dataForFrontend = [
                'eps' => $eps,
                'documento_paciente' => $tiposDocumentoPaciente,
                'documento_pagador' => $tiposDocumentoPagador,
                'paises' => $paises,
                'municipios' => $municipios,
                'timestamp' => now()->toIso8601String(), // Para saber cuándo se generaron los datos
            ];

            Log::info('[PrepareFrontendCacheDataJob] Datos recolectados exitosamente.');

            // Opcional: Guardar en la caché de Laravel para acceso más rápido en futuras peticiones
            // Cache::put('frontend_static_data', $this->dataForFrontend, now()->addHours(24));

        } catch (\Exception $e) {
            Log::error('[PrepareFrontendCacheDataJob] Error al recolectar datos: ' . $e->getMessage());
            $this->dataForFrontend = [
                'error' => 'No se pudieron cargar los datos.',
                'tipos_documento' => [
    [
        'nombre'            => 'Cédula de Ciudadanía',
        'cod_rips'          => 'CC',
        'cod_dian'          => '13',
        'es_nacional'       => true,
        'es_paciente'       => true,
        'es_pagador'        => true,
        'requiere_acudiente'=> false,
        'edad_minima'       => 18,
        'edad_maxima'       => 130,
        'regex_validacion'  => '^[0-9]{5,11}$',
        'nivel'             => 11,

    ],  [
        'nombre'            => 'Registro Civil',
        'cod_rips'          => 'RC',
        'cod_dian'          => '11',
        'es_nacional'       => true,
        'es_paciente'       => true,
        'es_pagador'        => false,
        'requiere_acudiente'=> true,
        'edad_minima'       => 0,
        'edad_maxima'       => 6,
        'regex_validacion'  => '^[0-9]{6,11}$',
        'nivel'             => 3

    ],
    [
        'nombre'            => 'Tarjeta de Identidad',
        'cod_rips'          => 'TI',
        'cod_dian'          => '12',
        'es_nacional'       => true,
        'es_paciente'       => true,
        'es_pagador'        => false,
        'requiere_acudiente'=> true,
        'edad_minima'       => 7,
        'edad_maxima'       => 17,
        'regex_validacion'  => '^[0-9]{10,11}$',
        'nivel'             => 5,

    ]
],
                'paises' => [],
                'municipios' => [],
            ];
        }
    }
}
