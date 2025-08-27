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
        // Crear modalidades de atención
        $modalidades = [
            ['nombre' => 'Consulta Externa'],
            ['nombre' => 'Urgencias'],
            ['nombre' => 'Hospitalización'],
            ['nombre' => 'Cirugía'],
            ['nombre' => 'Terapia Física'],
        ];

        foreach ($modalidades as $modalidad) {
            \App\Models\ModalidadAtencion::create($modalidad);
        }
    }
}
