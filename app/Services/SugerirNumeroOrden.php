<?php

namespace App\Services;

use App\Models\Orden;

class SugerirNumeroOrden
{
    public static function paraSede(int $sedeId): string
    {
        $numeros = Orden::withTrashed()
            ->where('sede_id', $sedeId)
            ->orderByDesc('id')
            ->limit(1000)
            ->pluck('numero');

        $ultimoNumero = $numeros->first();
        $siguiente = '1';

        if ($ultimoNumero !== null && preg_match('/^(.*?)(\d+)$/u', (string) $ultimoNumero, $coincidencias)) {
            $siguiente = $coincidencias[1]
                .str_pad(
                    (string) ((int) $coincidencias[2] + 1),
                    strlen($coincidencias[2]),
                    '0',
                    STR_PAD_LEFT
                );
        } else {
            foreach ($numeros as $numero) {
                if (ctype_digit((string) $numero)) {
                    $siguiente = (string) ((int) $numero + 1);
                    break;
                }
            }
        }

        while (Orden::withTrashed()->where('sede_id', $sedeId)->where('numero', $siguiente)->exists()) {
            if (! preg_match('/^(.*?)(\d+)$/u', $siguiente, $coincidencias)) {
                $siguiente = '1';

                continue;
            }

            $siguiente = $coincidencias[1]
                .str_pad(
                    (string) ((int) $coincidencias[2] + 1),
                    strlen($coincidencias[2]),
                    '0',
                    STR_PAD_LEFT
                );
        }

        return $siguiente;
    }
}
