<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ServicioHabilitadoSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas/REF_Servicios.csv');
        $file = fopen($filePath, 'r');

        if ($file === false) {
            throw new RuntimeException("Unable to open services reference file: {$filePath}");
        }

        $groupCodes = [
            'CONSULTA EXTERNA' => '01',
            'APOYO DIAGNOSTICO Y COMPLEMENTACION TERAPEUTICA' => '02',
            'INTERNACION' => '03',
            'QUIRURGICOS' => '04',
            'ATENCION INMEDIATA' => '05',
        ];
        $records = [];
        $lineNumber = 0;

        try {
            while (($line = fgetcsv($file, 0, '|')) !== false) {
                $lineNumber++;

                if ($lineNumber === 1 && isset($line[0]) && str_starts_with($line[0], "\u{FEFF}")) {
                    $line[0] = substr($line[0], 3);
                }

                if ($lineNumber === 1 && ($line[0] ?? null) === 'Id') {
                    continue;
                }

                if (count($line) < 5) {
                    throw new RuntimeException("Invalid services reference row at line {$lineNumber} in {$filePath}");
                }

                $codigo = trim($line[1]);
                $nombre = trim($line[2]);
                $grupo = trim($line[3]);
                if ($codigo === '' || $nombre === '' || !ctype_digit($codigo)) {
                    throw new RuntimeException("Invalid service code or name at line {$lineNumber} in {$filePath}");
                }

                if (!isset($groupCodes[$grupo])) {
                    throw new RuntimeException("Unknown service group '{$grupo}' at line {$lineNumber} in {$filePath}");
                }

                $records[] = [
                    'codigo' => (int) $codigo,
                    'nombre' => $nombre,
                    'grupo' => $grupo,
                    'codigo_grupo' => $groupCodes[$grupo],
                    'activo' => in_array((int) $codigo, [706, 712], true),
                ];
            }
        } finally {
            fclose($file);
        }

        if ($records === []) {
            throw new RuntimeException("No services were found in reference file: {$filePath}");
        }

        DB::table('servicios_habilitados')->upsert(
            $records,
            ['codigo'],
            ['nombre', 'grupo', 'codigo_grupo', 'activo']
        );
    }
}
