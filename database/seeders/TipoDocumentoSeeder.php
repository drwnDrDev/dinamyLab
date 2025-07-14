<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $documentos = [
            // ------------- CON EQUIVALENTE DIAN -------------
            [
                'nombre'            => 'Registro Civil',
                'cod_rips'          => 'RC',
                'cod_dian'          => '11',
                'es_nacional'       => true,
                'edad_minima'       => 0,
                'edad_maxima'       => 6,
                'unidad_edad'       => 1,                       // Años
                'regex_validacion'  => '^[0-9]{6,11}$',
                'requiere_acudiente'=> true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Tarjeta de Identidad',
                'cod_rips'          => 'TI',
                'cod_dian'          => '12',
                'es_nacional'       => true,
                'edad_minima'       => 7,
                'edad_maxima'       => 17,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[0-9]{10,11}$',
                'requiere_acudiente'=> true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Cédula de Ciudadanía',
                'cod_rips'          => 'CC',
                'cod_dian'          => '13',
                'es_nacional'       => true,
                'edad_minima'       => 18,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[0-9]{5,11}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Cédula de Extranjería',
                'cod_rips'          => 'CE',
                'cod_dian'          => '22',
                'es_nacional'       => true,                    // Expedida por Colombia
                'edad_minima'       => 18,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[A-Z0-9]{6,15}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'NIT',
                'cod_rips'          => 'NI',
                'cod_dian'          => '31',
                'es_nacional'       => true,
                'edad_minima'       => 0,                       // No aplica
                'edad_maxima'       => 0,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[0-9]{3,13}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Pasaporte',
                'cod_rips'          => 'PA',
                'cod_dian'          => '41',
                'es_nacional'       => false,
                'edad_minima'       => 0,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[A-Z0-9]{3,20}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Documento Extranjero',
                'cod_rips'          => 'DE',
                'cod_dian'          => '42',
                'es_nacional'       => false,
                'edad_minima'       => 0,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[A-Z0-9]{3,25}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'NIT de otro país',
                'cod_rips'          => 'NI-EXT',
                'cod_dian'          => '50',
                'es_nacional'       => false,
                'edad_minima'       => 0,
                'edad_maxima'       => 0,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[A-Z0-9]{9,16}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'NUIP',
                'cod_rips'          => 'NU',
                'cod_dian'          => '91',
                'es_nacional'       => true,
                'edad_minima'       => 0,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[0-9]{10}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],

            // ------------- SOLO RIPS (sin equivalente DIAN) -------------
            [
                'nombre'            => 'Menor sin identificación',
                'cod_rips'          => 'MS',
                'cod_dian'          => null,                    // ← haz nullable la columna
                'es_nacional'       => true,
                'edad_minima'       => 0,
                'edad_maxima'       => 17,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[0-9]{5}[A-Z]{1}[0-9]{4}$',
                'requiere_acudiente'=> true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Adulto sin identificación',
                'cod_rips'          => 'AS',
                'cod_dian'          => null,
                'es_nacional'       => true,
                'edad_minima'       => 18,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[0-9]{5}[A-Z]{1}[0-9]{4}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Carné Diplomático',
                'cod_rips'          => 'CD',
                'cod_dian'          => null,
                'es_nacional'       => false,
                'edad_minima'       => 0,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[A-Z0-9]{6,15}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nombre'            => 'Salvoconducto',
                'cod_rips'          => 'SC',
                'cod_dian'          => null,
                'es_nacional'       => false,
                'edad_minima'       => 0,
                'edad_maxima'       => 130,
                'unidad_edad'       => 1,
                'regex_validacion'  => '^[A-Z0-9]{6,15}$',
                'requiere_acudiente'=> false,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        // Inserta o actualiza basado en cod_rips (clave única)
        DB::table('tipo_documentos')
            ->upsert($documentos, ['cod_rips'], [
                'nombre', 'cod_dian', 'es_nacional', 'edad_minima',
                'edad_maxima', 'unidad_edad', 'regex_validacion',
                'requiere_acudiente', 'updated_at'
            ]);
    }
}
