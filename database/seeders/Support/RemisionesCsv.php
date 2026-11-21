<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Str;
use RuntimeException;
use SplFileObject;
use UnexpectedValueException;

class RemisionesCsv
{
    public static function filas(): array
    {
        $path = resource_path('utils/listas/remisiones.csv');
        if (! is_readable($path)) {
            throw new RuntimeException("No se puede leer el catálogo de remisiones: {$path}");
        }

        $csv = new SplFileObject($path);
        $csv->setCsvControl(',', '"', '');
        $cabecera = array_map(fn ($valor) => trim($valor ?? ''), $csv->fgetcsv());
        if ($cabecera !== ['CUPS', 'examen', 'valor', 'profesional  o IPS externa', 'Remision']) {
            throw new UnexpectedValueException('La cabecera del CSV de remisiones no es válida.');
        }

        $filas = [];
        while (! $csv->eof()) {
            $fila = $csv->fgetcsv();
            if ($fila === false || $fila === [null]) {
                continue;
            }
            if (count($fila) !== 5) {
                throw new UnexpectedValueException('Cada fila debe contener CUPS, examen, valor, IPS y Remision.');
            }
            [$cup, $nombre, $precio, $ips, $remitido] = array_map(fn ($valor) => trim($valor ?? ''), $fila);
            if (! preg_match('/^\d{1,20}$/', $cup) || $nombre === '' || $ips === ''
                || ! preg_match('/^\d+(?:\.\d{1,2})?$/', $precio)
                || ($remitido !== '' && ! preg_match('/^\d+(?:\.\d{1,2})?$/', $remitido))) {
                throw new UnexpectedValueException("Datos de remisión no válidos para el CUP {$cup}.");
            }

            $filas[] = [
                'cup' => $cup,
                'nombre' => $nombre,
                'precio' => $precio,
                'valor_remitido' => $remitido === '' ? null : $remitido,
                'ips' => array_unique(array_map(
                    fn ($nombreIps) => Str::title(mb_strtolower(trim($nombreIps))),
                    preg_split('/\s+-\s+/u', $ips)
                )),
            ];
        }

        return $filas;
    }
}
