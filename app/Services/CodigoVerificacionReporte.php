<?php

namespace App\Services;

class CodigoVerificacionReporte
{
    /**
     * Calcula un código estable a partir del contenido clínico del reporte.
     * No incluye la fecha de emisión ni las imágenes, para que pueda recalcularse.
     */
    public function generar(array $reporte): string
    {
        $examenes = array_map(function (array $examen): array {
            $parametros = [];

            foreach (($examen['grupos'] ?? []) as $grupo) {
                foreach (($grupo['parametros'] ?? []) as $parametro) {
                    $parametros[] = [
                        'nombre' => $parametro['nombre'] ?? null,
                        'resultado' => $parametro['resultado'] ?? null,
                        'unidades' => $parametro['unidades'] ?? null,
                        'referencia' => $parametro['referencia'] ?? null,
                    ];
                }
            }

            return [
                'procedimiento_id' => $examen['procedimiento_id'] ?? null,
                'nombre' => $examen['nombre'] ?? null,
                'fecha_validacion' => $examen['fecha_validacion'] ?? null,
                'parametros' => $parametros,
            ];
        }, $reporte['examenes'] ?? []);

        $ordenes = array_values(array_unique(array_map(
            fn (array $examen) => $examen['orden_numero'] ?? null,
            $reporte['examenes'] ?? [],
        )));
        sort($ordenes);

        $canonico = [
            'v' => 1,
            'paciente' => $reporte['paciente']['documento'] ?? null,
            'ordenes' => $ordenes,
            'examenes' => $examenes,
        ];

        $json = json_encode(
            $canonico,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE,
        ) ?: '';
        $codigo = strtoupper(substr(
            hash_hmac('sha256', $json, (string) config('reportes.clave_verificacion')),
            0,
            12,
        ));

        return implode('-', str_split($codigo, 4));
    }
}
