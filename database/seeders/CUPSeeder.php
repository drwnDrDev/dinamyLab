<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // For logging potential issues
use SplFileObject; // A more memory-efficient way to read large files

class CUPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas_de_referencia/cups.csv');
        $chunkSize = 1000; // Define your chunk size

        if (!file_exists($filePath)) {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
            return;
        }

        $this->command->info('Starting CUPs seeding...');

        // Use SplFileObject for memory efficiency with large files
        $file = new SplFileObject($filePath, 'r');
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        $now = now();
        $recordsToInsert = [];
        $totalRecords = 0;

        DB::beginTransaction(); // Start a database transaction

        try {
            while (!$file->eof()) {
                $row = $file->current();

                // Ensure the row is not empty and has enough columns
                if (is_array($row) && count($row) >= 2) { // Assuming at least 'codigo' and 'nombre' are always present
                    $recordsToInsert[] = [
                        'codigo' => $row[0] ?? null,
                        'nombre' => $row[1] ?? null,
                        'grupo' => $row[2] ?? null,
                        'activo' => false, // Default value
                        'nivel' => 1,      // Default value
       
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $totalRecords++;
                }

                if (count($recordsToInsert) >= $chunkSize) {
                    DB::table('codigo_cups')->insert($recordsToInsert);
                    $recordsToInsert = []; // Clear the array for the next chunk
                    $this->command->info("Inserted {$chunkSize} records. Total: {$totalRecords}");
                }

                $file->next();
            }

            // Insert any remaining records
            if (!empty($recordsToInsert)) {
                DB::table('codigo_cups')->insert($recordsToInsert);
                $this->command->info("Inserted remaining " . count($recordsToInsert) . " records. Total: {$totalRecords}");
            }

            DB::commit(); // Commit the transaction if all insertions are successful
            $this->command->info("CUPs seeding completed successfully! Total records processed: {$totalRecords}");

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            Log::error("CUPs Seeder failed: " . $e->getMessage());
            $this->command->error("CUPs Seeder failed: " . $e->getMessage());
            $this->command->error("Rolling back changes.");
        }

        $filePath = base_path('resources/utils/tablas_de_referencia/CIE10.csv');
        $chunkSize = 2000; // Define your chunk size
        if (!file_exists($filePath)) {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
            return;
        }

        $this->command->info('Starting CIE10 seeding...');
        // Use SplFileObject for memory efficiency with large files
        $file = new SplFileObject($filePath, 'r');
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl(';'); // Set semicolon as separator

        $now = now();
        $recordsToInsert = [];
        $totalRecords = 0;

        DB::beginTransaction(); // Start a database transaction

        try {
            while (!$file->eof()) {
                $row = $file->current();

                // Ensure the row is not empty and has enough columns
                if (is_array($row) && count($row) >= 2) { // Assuming at least 'codigo' and 'nombre' are always present
                    $recordsToInsert[] = [
                        'codigo' => $row[0] ?? null,
                        'nombre' => $row[1] ?? null,
                        'descripcion' => $row[2] ?? null,
                        'edad_minima' => $row[4] ?? null,
                        'edad_maxima' => $row[5] >250 ? 250 : $row[5] ?? null, // Cap max age to 250
                        'sexo_aplicable' => $row[10] ?? null,
                        'grupo' => $row[7] ?? null,
                        'sub_grupo' => $row[9] ?? null,
                        'grupo_mortalidad' => $row[6]=='' ? null :$row[6],
                        'capitulo' => $row[8] ?? null,
                        'activo' => false, // Default value
                        'nivel' => 1,      // Default value
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $totalRecords++;
                }

                if (count($recordsToInsert) >= $chunkSize) {
                    DB::table('codigo_diagnosticos')->insert($recordsToInsert);
                    $recordsToInsert = []; // Clear the array for the next chunk
                    $this->command->info("Inserted {$chunkSize} records. Total: {$totalRecords}");
                }

                $file->next();
            }

            // Insert any remaining records
            if (!empty($recordsToInsert)) {
                DB::table('codigo_diagnosticos')->insert($recordsToInsert);
                $this->command->info("Inserted remaining " . count($recordsToInsert) . " records. Total: {$totalRecords}");
            }

            DB::commit(); // Commit the transaction if all insertions are successful
            $this->command->info("CIE10 seeding completed successfully! Total records processed: {$totalRecords}");

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            Log::error("CIE10 Seeder failed: " . $e->getMessage());
            $this->command->error("CIE10 Seeder failed: " . $e->getMessage());
            $this->command->error("Rolling back changes.");
        }

    // }
}
}
