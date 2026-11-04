<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RelacionesCupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('resources/utils/tablas/REF_CUPS_CIE10.csv');
        if (file_exists($filePath)) {
            $availableCups = array_fill_keys(DB::table('codigo_cups')->pluck('codigo')->all(), true);
            $availableDiagnoses = array_fill_keys(DB::table('codigo_diagnosticos')->pluck('codigo')->all(), true);
            $existingRelations = [];
            foreach (DB::table('cup_diagnostico')->get(['cup', 'cie']) as $relation) {
                $existingRelations[$relation->cup . '|' . $relation->cie] = true;
            }

            $file = fopen($filePath, 'r');
            $firstRow = true;
            $skippedMissingReferences = 0;

            while (($data = fgetcsv($file, 0, "|")) !== false) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                if (isset($data[1]) && isset($data[2])) {
                    $cup = trim($data[1]);
                    $cie = trim($data[2]);
                    $missingReferences = [];

                    if (!isset($availableCups[$cup])) {
                        $missingReferences[] = 'CUP';
                    }

                    if (!isset($availableDiagnoses[$cie])) {
                        $missingReferences[] = 'CIE';
                    }

                    if ($missingReferences !== []) {
                        $skippedMissingReferences++;
                        Log::warning('Skipped CUP-diagnosis relation with missing catalog references.', [
                            'cup' => $cup,
                            'cie' => $cie,
                            'missing' => $missingReferences,
                        ]);
                        continue;
                    }

                    $relationKey = $cup . '|' . $cie;
                    if (isset($existingRelations[$relationKey])) {
                        continue;
                    }

                    DB::table('cup_diagnostico')->insert([
                        'cup' => $cup,
                        'cie' => $cie,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $existingRelations[$relationKey] = true;
                }
            }

            if ($skippedMissingReferences > 0) {
                $message = "Skipped {$skippedMissingReferences} CUP-diagnosis relations because a CUP or diagnosis code is missing from its catalog.";
                $this->command?->warn($message);
            }

            fclose($file);
        } else {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
        }

        $filePath = base_path('resources/utils/tablas/REF_FinalidadCups.csv');
        if (file_exists($filePath)) {
            $availableCups = array_fill_keys(DB::table('codigo_cups')->pluck('codigo')->all(), true);
            $availableFinalidades = array_fill_keys(DB::table('finalidades')->pluck('codigo')->all(), true);
            $existingRelations = [];
            foreach (DB::table('cup_finalidad')->get(['cup', 'finalidad']) as $relation) {
                $existingRelations[$relation->cup . '|' . $relation->finalidad] = true;
            }

            $file = fopen($filePath, 'r');
            $firstRow = true;
            $skippedMissingReferences = 0;

            while (($data = fgetcsv($file, 0, "|")) !== false) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                if (isset($data[1]) && isset($data[2])) {
                    $cup = trim($data[2]);
                    $finalidad = trim($data[1]);
                    $missingReferences = [];

                    if (!isset($availableCups[$cup])) {
                        $missingReferences[] = 'CUP';
                    }

                    if (!isset($availableFinalidades[$finalidad])) {
                        $missingReferences[] = 'finalidad';
                    }

                    if ($missingReferences !== []) {
                        $skippedMissingReferences++;
                        Log::warning('Skipped CUP-finalidad relation with missing catalog references.', [
                            'cup' => $cup,
                            'finalidad' => $finalidad,
                            'missing' => $missingReferences,
                        ]);
                        continue;
                    }

                    $relationKey = $cup . '|' . $finalidad;
                    if (isset($existingRelations[$relationKey])) {
                        continue;
                    }

                    DB::table('cup_finalidad')->insert([
                        'cup' => $cup,
                        'finalidad' => $finalidad,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $existingRelations[$relationKey] = true;
                }
            }

            if ($skippedMissingReferences > 0) {
                $message = "Skipped {$skippedMissingReferences} CUP-finalidad relations because a CUP or finalidad code is missing from its catalog.";
                $this->command?->warn($message);
            }

            fclose($file);
        } else {
            Log::error("CSV file not found at: {$filePath}");
            $this->command->error("CSV file not found. Please check the path: {$filePath}");
        }
    }
}
