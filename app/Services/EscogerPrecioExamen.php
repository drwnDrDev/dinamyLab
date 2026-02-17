<?php

namespace App\Services;

use App\Models\Convenio;
use App\Models\Examen;
use App\Models\Sede;
use App\Models\Tarifa;
use Illuminate\Support\Facades\Log;

class EscogerPrecioExamen
{
    /**
     * Obtiene el precio inteligente de un examen según convenio y sede.
     *
     * Orden de prioridad:
     * 1. Tarifa específica por convenio + sede
     * 2. Tarifa por sede (sin convenio)
     * 3. Precio base del examen (examen->valor)
     *
     * @param Examen $examen
     * @param Convenio $convenio
     * @param Sede $sede
     * @return array Con estructura: ['precio' => float, 'tipo_tarifa' => string]
     */
    public static function obtener(Examen $examen, Convenio $convenio, Sede $sede): array
    {
        try {
            // 1. Buscar tarifa específica por convenio + sede
            $tarifa = self::buscarTarifaPorConvenioYSede($examen, $convenio, $sede);
            if ($tarifa) {
                Log::info('Tarifa encontrada por convenio y sede.', [
                    'examen_id' => $examen->id,
                    'convenio_id' => $convenio->id,
                    'sede_id' => $sede->id,
                    'tarifa_id' => $tarifa->id,
                    'precio' => $tarifa->precio
                ]);

                return [
                    'precio' => (float) $tarifa->precio,
                    'tipo_tarifa' => 'por_convenio_y_sede',
                    'tarifa_id' => $tarifa->id
                ];
            }

            // 2. Buscar tarifa solo por sede (sin vincular a convenio)
            $tarifa = self::buscarTarifaPorSede($examen, $sede);
            if ($tarifa) {
                Log::info('Tarifa encontrada por sede únicamente.', [
                    'examen_id' => $examen->id,
                    'sede_id' => $sede->id,
                    'tarifa_id' => $tarifa->id,
                    'precio' => $tarifa->precio
                ]);

                return [
                    'precio' => (float) $tarifa->precio,
                    'tipo_tarifa' => 'por_sede',
                    'tarifa_id' => $tarifa->id
                ];
            }

            // 3. Retornar precio base del examen
            Log::info('Usando precio base del examen (no se encontró tarifa).', [
                'examen_id' => $examen->id,
                'convenio_id' => $convenio->id,
                'sede_id' => $sede->id,
                'precio_base' => $examen->valor
            ]);

            return [
                'precio' => (float) $examen->valor,
                'tipo_tarifa' => 'precio_base',
                'tarifa_id' => null
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener precio del examen.', [
                'examen_id' => $examen->id,
                'convenio_id' => $convenio->id,
                'sede_id' => $sede->id,
                'error' => $e->getMessage()
            ]);

            // Fallback al precio base en caso de error
            return [
                'precio' => (float) $examen->valor,
                'tipo_tarifa' => 'precio_base_por_error',
                'tarifa_id' => null
            ];
        }
    }

    /**
     * Busca una tarifa específica para un examen en una sede que esté vinculada al convenio.
     *
     * @param Examen $examen
     * @param Convenio $convenio
     * @param Sede $sede
     * @return Tarifa|null
     */
    private static function buscarTarifaPorConvenioYSede(Examen $examen, Convenio $convenio, Sede $sede): ?Tarifa
    {
        return Tarifa::query()
            ->where('tarifable_type', 'App\\Models\\Examen')
            ->where('tarifable_id', $examen->id)
            ->where('sede_id', $sede->id)
            ->whereHas('convenios', function ($query) use ($convenio) {
                $query->where('convenios.id', $convenio->id);
            })
            ->first();
    }

    /**
     * Busca una tarifa para un examen en una sede (sin validar convenio).
     *
     * @param Examen $examen
     * @param Sede $sede
     * @return Tarifa|null
     */
    private static function buscarTarifaPorSede(Examen $examen, Sede $sede): ?Tarifa
    {
        return Tarifa::query()
            ->where('tarifable_type', 'App\\Models\\Examen')
            ->where('tarifable_id', $examen->id)
            ->where('sede_id', $sede->id)
            ->first();
    }

    /**
     * Obtiene el precio final de un examen (alias para compatibilidad).
     * Retorna solo el valor numérico.
     *
     * @param Examen $examen
     * @param Convenio $convenio
     * @param Sede $sede
     * @return float
     */
    public static function obtenerPrecio(Examen $examen, Convenio $convenio, Sede $sede): float
    {
        $resultado = self::obtener($examen, $convenio, $sede);
        return $resultado['precio'];
    }
}
