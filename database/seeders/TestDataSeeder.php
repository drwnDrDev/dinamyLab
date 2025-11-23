<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Seed data mínima necesaria para tests.
     */
    public function run(): void
    {
        // Finalidades
        DB::table('finalidades')->insert([
            ['codigo' => '01', 'nombre' => 'Atención del parto (puerperio)', 'activo' => true, 'nivel' => 1],
            ['codigo' => '02', 'nombre' => 'Atención del recién nacido', 'activo' => true, 'nivel' => 1],
            ['codigo' => '03', 'nombre' => 'Atención en planificación familiar', 'activo' => true, 'nivel' => 1],
            ['codigo' => '04', 'nombre' => 'Detección de alteraciones de crecimiento y desarrollo del menor de diez años', 'activo' => true, 'nivel' => 1],
            ['codigo' => '05', 'nombre' => 'Detección de alteración del desarrollo del joven', 'activo' => true, 'nivel' => 1],
            ['codigo' => '06', 'nombre' => 'Detección de alteraciones del embarazo', 'activo' => true, 'nivel' => 1],
            ['codigo' => '07', 'nombre' => 'Detección de alteraciones del adulto', 'activo' => true, 'nivel' => 1],
            ['codigo' => '08', 'nombre' => 'Detección de alteraciones de agudeza visual', 'activo' => true, 'nivel' => 1],
            ['codigo' => '09', 'nombre' => 'Detección de enfermedad profesional', 'activo' => true, 'nivel' => 1],
            ['codigo' => '10', 'nombre' => 'No aplica', 'activo' => true, 'nivel' => 1],
            ['codigo' => '11', 'nombre' => 'Patología de cuello uterino', 'activo' => true, 'nivel' => 1],
            ['codigo' => '12', 'nombre' => 'Promoción y fomento de la salud', 'activo' => true, 'nivel' => 1],
            ['codigo' => '13', 'nombre' => 'Protección específica', 'activo' => true, 'nivel' => 1],
            ['codigo' => '14', 'nombre' => 'Diagnóstico', 'activo' => true, 'nivel' => 1],
            ['codigo' => '15', 'nombre' => 'Tratamiento', 'activo' => true, 'nivel' => 1],
        ]);

        // Modalidades de atención
        DB::table('modalidades_atencion')->insert([
            ['codigo' => '01', 'nombre' => 'Intramural', 'nivel' => 1, 'activo' => true],
            ['codigo' => '02', 'nombre' => 'Extramural unidad móvil', 'nivel' => 1, 'activo' => true],
            ['codigo' => '03', 'nombre' => 'Extramural domiciliaria', 'nivel' => 1, 'activo' => true],
        ]);

        // Vías de ingreso
        DB::table('vias_ingreso')->insert([
            ['codigo' => '01', 'nombre' => 'DEMANDA ESPONTANEA', 'nivel' => 1, 'activo' => true],
            ['codigo' => '02', 'nombre' => 'DERIVADO DE CONSULTA EXTERNA', 'nivel' => 1, 'activo' => true],
            ['codigo' => '03', 'nombre' => 'DERIVADO DE URGENCIAS', 'nivel' => 1, 'activo' => true],
        ]);

        // Servicios habilitados (706 es laboratorio clínico)
        DB::table('servicios_habilitados')->insert([
            ['codigo' => 706, 'nombre' => 'LABORATORIO CLINICO', 'grupo' => 'APOYO DIAGNOSTICO Y COMPLEMENTACION TERAPEUTICA', 'codigo_grupo' => '07', 'activo' => true],
            ['codigo' => 701, 'nombre' => 'DIAGNOSTICO CARDIOVASCULAR', 'grupo' => 'APOYO DIAGNOSTICO Y COMPLEMENTACION TERAPEUTICA', 'codigo_grupo' => '07', 'activo' => true],
        ]);

        // Códigos diagnóstico CIE-10 básicos
        DB::table('codigo_diagnosticos')->insert([
            ['codigo' => 'Z017', 'nombre' => 'Examen de ojos y de la visión','descripcion'=>"Esta es la historia de un sabado", 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'Z000', 'nombre' => 'Examen médico general','descripcion'=>"Esta es la historia de un sabado", 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'Z010', 'nombre' => 'Examen de oídos y de la audición','descripcion'=>"Esta es la historia de un sabado", 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
