<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // For logging potential issues
use SplFileObject; // A more memory-efficient way to read large files


class RelacionesCupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas/REF_CUPS_CIE10.csv');
        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r');
            $firstRow = true;

            while (($data = fgetcsv($file, 0, "|")) !== false) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                if (isset($data[1]) && isset($data[2])) {
                    DB::table('cup_diagnostico')->insert([
                        'cup' => trim($data[1]),
                        'cie' => trim($data[2]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            fclose($file);
        }else {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
        }
        $filePath = base_path('resources/utils/tablas/REF_FinalidadCups.csv');
        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r');
            $firstRow = true;
            while (($data = fgetcsv($file, 0, "|")) !== false) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                if (isset($data[1]) && isset($data[2])) {
                    DB::table('cup_finalidad')->insert([
                        'cup' => trim($data[2]),
                        'finalidad' => trim($data[1]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            fclose($file);
        }else {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
        }
}
}
