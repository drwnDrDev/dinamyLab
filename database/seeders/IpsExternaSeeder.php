<?php

namespace Database\Seeders;

use App\Models\IpsExterna;
use Database\Seeders\Support\RemisionesCsv;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IpsExternaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (RemisionesCsv::filas() as $fila) {
                foreach ($fila['ips'] as $nombre) {
                    IpsExterna::firstOrCreate(['nombre' => $nombre]);
                }
            }
        });
    }
}
