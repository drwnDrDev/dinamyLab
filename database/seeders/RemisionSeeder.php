<?php

namespace Database\Seeders;

use App\Models\Remision;
use Database\Seeders\Support\RemisionesCsv;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (RemisionesCsv::filas() as $fila) {
                Remision::updateOrCreate(['cup' => $fila['cup']], ['nombre' => $fila['nombre']]);
            }
        });
    }
}
