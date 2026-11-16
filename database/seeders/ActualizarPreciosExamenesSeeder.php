<?php

namespace Database\Seeders;

use App\Models\Examen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use SplFileObject;
use UnexpectedValueException;

class ActualizarPreciosExamenesSeeder extends Seeder
{
    private const EXAMENES_EXISTENTES = [
        'GLICEMIA' => 'Glicemia',
        'COLESTEROL' => 'Colesterol Total',
        'COLESTEROL HDL' => 'Colesterol HDL',
        'COLESTEROL LDL' => 'Colesterol LDL',
        'TRIGLICERIDOS' => 'Triglicéridos',
        'ACIDO URICO' => 'Ácido Úrico',
        'PARCIAL DE ORINA' => 'Uroanalisis',
        'COPROLOGICO' => 'Coproanalisis',
        'K O H PIEL Y UNAS' => 'Koh Piel y Uñas',
        'FROTIS DE GARGANTA' => 'Frotis de garganta',
        'R A TEST FACTOR REUMATOIDEO' => 'factor reumatoideo',
        'PROTEINA C REACTIVA PCR' => 'Proteína C reactiva',
        'ANTIESTREPTOLISINA' => 'Antiestreptolisina',
        'GLICEMIA POST PRANDIAL' => 'Glicemia Post-prandial',
        'FROTIS VAGINAL' => 'Frotis de flujo vaginal',
        'FROTIS DE SECRESION URETRAL' => 'Frotis de secreción uretral',
        'GRAM DE CUALQUIER MUESTRA' => 'Frotis de secreción',
        'SEROLOGIA' => 'Serologia (Prueba no treponemica) VDRL',
        'PRUEBA DE EMBARAZO' => 'Prueba de embarazo',
        'EOSINOFILOS EN MOCO NASAL' => 'Eosinofilos en moco nasal',
    ];

    public function run(): void
    {
        $path = base_path('resources/utils/listas/examenes.csv');

        if (! is_readable($path)) {
            throw new RuntimeException("No se puede leer el archivo de precios: {$path}");
        }

        $csv = new SplFileObject($path);
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $csv->setCsvControl(',', '"', '');

        if ($csv->fgetcsv() !== ['EXAMEN', 'PRECIO']) {
            throw new UnexpectedValueException('El CSV debe iniciar con las columnas EXAMEN,PRECIO.');
        }

        $csv->seek(1);

        [$actualizados, $sinCorrespondencia] = DB::transaction(function () use ($csv): array {
            $actualizados = 0;
            $sinCorrespondencia = [];

            while (! $csv->eof()) {
                $fila = $csv->fgetcsv();

                if ($fila === false || $fila === [null]) {
                    continue;
                }

                if (count($fila) !== 2 || ! is_string($fila[0]) || ! is_string($fila[1])) {
                    throw new UnexpectedValueException('El CSV contiene una fila que no tiene exactamente EXAMEN y PRECIO.');
                }

                [$nombreCsv, $precio] = array_map('trim', $fila);

                if (! preg_match('/^\d+(?:\.\d{1,2})?$/', $precio)) {
                    throw new UnexpectedValueException("Precio no válido para el examen {$nombreCsv}: {$precio}");
                }

                $nombreExamen = self::EXAMENES_EXISTENTES[$this->normalizar($nombreCsv)] ?? null;

                if ($nombreExamen === null) {
                    $sinCorrespondencia[] = $nombreCsv;
                    continue;
                }

                $examenes = Examen::query()->where('nombre', $nombreExamen);

                if (! $examenes->exists()) {
                    $sinCorrespondencia[] = "{$nombreCsv} (examen esperado: {$nombreExamen})";
                    continue;
                }

                $examenes->update(['valor' => number_format((float) $precio, 2, '.', '')]);
                $actualizados++;
            }

            return [$actualizados, $sinCorrespondencia];
        });

        $this->command?->info("Precios actualizados para {$actualizados} exámenes existentes.");

        foreach ($sinCorrespondencia as $nombre) {
            $this->command?->warn("No se encontró una correspondencia para «{$nombre}»; no se creó ningún examen.");
        }
    }

    private function normalizar(string $nombre): string
    {
        $nombre = strtoupper(Str::ascii($nombre));
        $nombre = preg_replace('/[^A-Z0-9]+/', ' ', $nombre) ?? '';

        return trim(preg_replace('/\s+/', ' ', $nombre) ?? '');
    }
}
