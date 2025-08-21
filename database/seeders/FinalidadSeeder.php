<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Finalidad;

class FinalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas_de_referencia/TablaReferencia_RIPSFinalidadConsultaVersion2__1.csv');
        if (!file_exists($filePath)) {
             $this->command->error("CSV file not found. Please check the path: {$filePath}");
            return;
        }

        $file = fopen($filePath, 'r');
        if ($file === false) {
            $this->command->error("Could not open the CSV file at: {$filePath}");
            return;
        }




        while (($line = fgetcsv($file, 1000, ";")) !== false) {
            // Asegúrate de que la línea tenga el formato esperado
            if (count($line) < 2) {
                continue;
            }

            Finalidad::create([
                'codigo' => $line[0],
                'nombre' => $line[1]
            ]);
        }

        fclose($file);
    }
}
