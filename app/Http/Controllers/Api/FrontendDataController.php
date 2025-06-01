<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PrepareFrontendCacheDataJob;
use App\Enums\TipoDocumento; // Alternativa sin Job
use App\Models\Pais; // Alternativa sin Job
use App\Models\Municipio; // Alternativa sin Job
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache; // Si usas caché con el Job
use Illuminate\Support\Facades\Log;

class FrontendDataController extends Controller
{
    /**
     * Proporciona los datos necesarios para el frontend (localStorage).
     */
    public function getStaticData(): JsonResponse
    {
        Log::info('[FrontendDataController] Solicitud de datos estáticos recibida.');

        // Opción 1: Usar el Job (ejecutándolo sincrónicamente para este caso)
        // $job = new PrepareFrontendCacheDataJob();
        // $job->handle(); // Ejecuta el método handle directamente
        // $data = $job->dataForFrontend;
        // return response()->json($data);

        // Opción 2: Lógica directamente en el controlador (más simple si no se necesita un Job complejo)
        // Esto es a menudo preferible si la lógica no es pesada o no necesita ser reutilizada en contextos de cola.
        try {
            // Opcional: Intentar obtener de la caché primero
            // $cachedData = Cache::get('frontend_static_data');
            // if ($cachedData) {
            //     Log::info('[FrontendDataController] Datos estáticos servidos desde caché.');
            //     return response()->json($cachedData);
            // }

            $tiposDocumento = TipoDocumento::forLocalStorage();
            $paises = Pais::select('id', 'nombre', 'codigo_iso')->orderBy('nombre')->get();
            $municipios = Municipio::select('id', 'nombre', 'departamento', 'pais_id')->orderBy('nombre')->get();

            $data = [
                'tipos_documento' => $tiposDocumento,
                'paises' => $paises,
                'municipios' => $municipios,
                'timestamp' => now()->toIso8601String(),
            ];

            // Opcional: Guardar en caché para la próxima vez
            // Cache::put('frontend_static_data', $data, now()->addHours(24)); // Cache por 24 horas

            Log::info('[FrontendDataController] Datos estáticos generados y servidos.');
            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('[FrontendDataController] Error al obtener datos estáticos: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al procesar la solicitud de datos.',
                'message' => $e->getMessage() // Solo en desarrollo, no exponer errores detallados en producción
            ], 500);
        }
    }
}
