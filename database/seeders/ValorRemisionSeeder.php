<?php

namespace Database\Seeders;

use App\Models\IpsExterna;
use App\Models\Remision;
use App\Models\ValorRemision;
use Database\Seeders\Support\RemisionesCsv;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValorRemisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->call([IpsExternaSeeder::class, RemisionSeeder::class]);
            $remisiones = Remision::all()->keyBy('cup');
            $ipsExternas = IpsExterna::all()->keyBy('nombre');

            foreach (RemisionesCsv::filas() as $fila) {
                foreach ($fila['ips'] as $nombre) {
                    ValorRemision::updateOrCreate([
                        'remision_id' => $remisiones[$fila['cup']]->id,
                        'ips_externa_id' => $ipsExternas[$nombre]->id,
                    ], [
                        'precio' => $fila['precio'],
                        'valor_remitido' => $fila['valor_remitido'],
                    ]);
                }
            }
        });
    }
}
