<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServicioHabilitado;

class ServicioHabilitadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas_de_referencia/TablaReferencia_Servicios.csv');
        if (!file_exists($filePath)) {
            return;
        }

        $file = fopen($filePath, 'r');
        if ($file === false) {
            return;
        }

        while (($line = fgetcsv($file, 1000, ";")) !== false) {
            // Asegúrate de que la línea tenga el formato esperado
            if (count($line) < 4) {
                continue;
            }

            ServicioHabilitado::create([
                'codigo' => $line[0],
                'nombre' => $line[1],
                'grupo' => $line[2],
                'codigo_grupo' => str_pad($line[3], 2, '0', STR_PAD_LEFT)
            ]);
        }

        fclose($file);
    }
}
