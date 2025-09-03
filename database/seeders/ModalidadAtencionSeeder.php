<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModalidadAtencionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lista ="01: Intramural; 02: Extramural unidad móvil 03: Extramural domiciliaria 04:
Extramural jornada de salud; 06: Telemedicina interactiva; 07: Telemedicina
no interactiva; 08: Telemedicina telexperticia; 09: Telemedicina
telemonitoreo";
        $modalidades = explode(";", $lista);


        foreach ($modalidades as $modalidad) {
            \App\Models\ModalidadAtencion::create([
                'codigo' => trim(explode(':', $modalidad)[0]),
                'nombre' => trim(explode(':', $modalidad)[1]),
                'nivel' => 1,
                'activo' => true
            ]);
            $this->command->info("Modalidad de atención {$modalidad} creada.");
        }
    }
}
