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
     * Este método contendrá la lógica para obtener los datos.
     */
    public function handle(): void
    {
        Log::info('[PrepareFrontendCacheDataJob] Iniciando la recolección de datos para localStorage.');

        try {
            $tiposDocumento = TipoDocumento::forLocalStorage();
            // Selecciona solo las columnas necesarias para optimizar
            $paises = Pais::select('id', 'nombre', 'codigo_iso')->orderBy('nivel','desc')->get()->toArray();
            $municipios = Municipio::select('id', 'municipio', 'departamento')->orderBy('nivel','desc')->get()->toArray();
            $eps = \App\Models\Eps::select('id', 'nombre')
                ->where('verificada', true) // Solo EPS verificadas
                ->orderBy('nombre')->get()->toArray();

            $this->dataForFrontend = [
                'tipos_documento' => $tiposDocumento,
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
                'tipos_documento' => [],
                'paises' => [],
                'municipios' => [],
            ];
        }
    }
}
