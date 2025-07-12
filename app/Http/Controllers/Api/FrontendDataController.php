<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PrepareFrontendCacheDataJob;
use App\TipoDocumento;
use App\Models\Pais;
use App\Models\Municipio;
use App\Models\Eps;
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
            $paises = Pais::select('nombre', 'codigo_iso')->orderBy('nivel', 'desc')->get();
            $municipios = Municipio::orderBy('nivel', 'desc')->get()->map(function($municipio) {
            return [
                'codigo' => $municipio->id,
                'municipio' => mb_strtolower($municipio->municipio, 'UTF-8'),
                'departamento' => mb_strtolower($municipio->departamento, 'UTF-8')
            ];
        });

            $eps = Eps::select('nombre', 'id')
            ->where('verificada', true) // Solo EPS verificadas
            ->orderBy('nombre')->get();

            $data = [
                'tipos_documento' => $tiposDocumento,
                'paises' => $paises,
                'municipios' => $municipios,
                'eps' => $eps,
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
