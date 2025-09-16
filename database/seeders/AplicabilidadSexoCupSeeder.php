<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CodigoCup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // For logging potential issues
use SplFileObject; // A more memory-efficient way to read large files

class AplicabilidadSexoCupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas/REF_CUPS.csv');
        $chunkSize = 1000; // Define your chunk size

        if (!file_exists($filePath)) {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
            return;
        }

        $this->command->info('Starting CUPs updating...');

        // Use SplFileObject for memory efficiency with large files
        $file = new SplFileObject($filePath, 'r');
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        $now = now();
        $totalRecords = 0;



        try {
            while (!$file->eof()) {
                $row = $file->current();

                $cupCurrent = explode('|', $row[0]);



                CodigoCup::updateOrCreate(
                    ['codigo' => $cupCurrent[1]],
                    [
                        'nombre' => "Buscar nombre: {$cupCurrent[1]}",
                        'sexo_aplicable' => $cupCurrent[5] == 'Z' ? 'A' : ($cupCurrent[5] ?? 'A'),
                        'qx' => $cupCurrent[4] === 'S' ? true : false,
                        'cobertura' => $cupCurrent[6] ?? null,
                        'updated_at' => $now,
                    ]
                );



                $file->next();
            }



            $this->command->info("CUPs seeding completed successfully! Total records processed: {$totalRecords}");

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            Log::error("CUPs Seeder failed: " . $e->getMessage());
            $this->command->error("CUPs Seeder failed: " . $e->getMessage());
            $this->command->error("Rolling back changes.");
        }

    }
}
