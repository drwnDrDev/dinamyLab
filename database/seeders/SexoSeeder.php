<?php

namespace Database\Seeders;

use App\Models\Sexo;
use Illuminate\Database\Seeder;

class SexoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['codigo' => 'F', 'nombre' => 'Femenino', 'cod_sexo' => '02'],
            ['codigo' => 'I', 'nombre' => 'Indeterminado o Intersexual', 'cod_sexo' => '03'],
            ['codigo' => 'M', 'nombre' => 'Masculino', 'cod_sexo' => '01'],
        ] as $sexo) {
            Sexo::updateOrCreate(
                ['codigo' => $sexo['codigo']],
                ['nombre' => $sexo['nombre'], 'cod_sexo' => $sexo['cod_sexo']]
            );
        }
    }
}
