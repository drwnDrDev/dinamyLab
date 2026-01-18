<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CodigoDiagnostico;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de códigos CIE-10 a activar con nivel 10
        $codigos = [
            'Z32',  // Examen y prueba de embarazo
            'N91',  // Amenorrea, sin otra especificación
            'N91',  // Amenorrea primaria
            'N91',  // Amenorrea secundaria
            'Z10',  // Examen de salud ocupacional
            'Z11',  // Pesquisa esp. otras enf. infecciosas
            'Z11',  // Pesquisa esp. enf. inf. intestinales
            'Z11',  // Pesquisa esp. otras enf. bacterianas
            'Z11',  // Pesquisa esp. enf. transm. sexual
            'Z11',  // Pesquisa esp. VIH
            'Z11',  // Pesquisa esp. otras enf. virales
            'Z01',  // Examen ginecológico (general)
            'Z00',  // Examen médico general
            'Z13',  // Pesquisa esp. diabetes mellitus
            'Z13',  // Pesquisa esp. tr. nutricionales/endocrinos
            'Z13',  // Pesquisa esp. otras enf. especificadas
            'Z12',  // Pesquisa esp. neoplasia de próstata
            'Z13',  // Pesquisa esp. tr. cardiovasculares
        ];

        // Eliminar duplicados
        $codigos = array_unique($codigos);

        // Buscar y activar cada código sin el punto
        foreach ($codigos as $codigo) {
            // Buscar el código con o sin punto
            $diagnostico = CodigoDiagnostico::where('codigo', 'LIKE', $codigo . '%')
                ->first();

            if ($diagnostico) {
                // Activarlo y asignar nivel 10
                $diagnostico->update([
                    'activo' => true,
                    'nivel' => 10
                ]);

                echo "✓ Activado: {$diagnostico->codigo} - {$diagnostico->descripcion}\n";
            } else {
                echo "✗ No encontrado: {$codigo}\n";
            }
        }

        echo "\n✓ Setup completado!\n";
    }
}
