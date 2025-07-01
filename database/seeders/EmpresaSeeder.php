<?php

namespace Database\Seeders;


use App\Models\Persona;

use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    $persona = Persona::create([
            'primer_nombre'=>'Claudia',
            'segundo_nombre'=>'Patricia',
            'primer_apellido'=>'Buitrago',
            'segundo_apellido'=>'Hernandez',
            'tipo_documento'=>'CC',
            'numero_documento'=>'51934571',
            'fecha_nacimiento'=>'1969-01-11',
            'sexo'=>'F',
            'nacional'=>true,
        ]);
        $extranjero = Persona::create([
            'primer_nombre'=>'Ronaldo',
            'primer_apellido'=>'Nazario',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123466789',
            'fecha_nacimiento'=>'1929-01-11',
            'sexo'=>'M',
            'nacional'=>false,
        ]);

        $extranjero->procedencia()->create([
            'pais_codigo_iso'=>'USA',
        ]);
        $extranjero->direccion()->create([
            'municipio_id'=>'11007',
            'direccion'=>'Calle 80 I sur # 81 J 36'
        ]);
        $persona->direccion()->create([
            'municipio_id'=>'11007',
            'direccion'=>'Diagonal 69 C sur # 78 C 36'
        ]);
        $persona->telefonos()->create([
            'numero'=>'3207001403',
        ]);
        $persona->emails()->create([
            'email'=>'ronaldmcdonalds@dinamycode.com'
        ]);
        $persona->redesSociales()->create([
            'nombre'=>'Linkedin',
            'url'=>'https://www.linkedin.com/in/claudia-buitrago-hernandez-123456789/',
            'perfil'=>'Claudia Buitrago Hernandez',
        ]);

        $empresa = \App\Models\Empresa::create([
            'nit'=>'51934571-8',
            'razon_social'=>'Laboratorio Claudia Buitrago',
            'nombre_comercial'=>'Biotek',

        ]);

        $empresa->telefonos()->create([
            'numero'=>'3207001403'
        ]);


        $sede = \App\Models\Sede::create([
            'nombre'=>'Biotek Bosa',
            'codigo_prestador'=>'110010822701',
            'logo'=>'biotek_logo.png',
            'empresa_id'=>$empresa->id,

        ]);
        $sede->telefonos()->create([
            'numero'=>'3207001403'
        ]);
        $sede->direccion()->create([
            'municipio_id'=>'11007',
            'direccion'=>'Diagonal 69 C sur # 78 C 36'
        ]);

        $sede2 = \App\Models\Sede::create([
            'nombre'=>'IPS Bosa',
            'codigo_prestador'=>'110010822703',
            'logo'=>'ryc.png',
            'empresa_id'=>$empresa->id,

        ]);

        $sede2->telefonos()->create(['numero'=>'3005705987']);
        $sede2->telefonos()->create(['numero'=>'3103213025']);
        $sede2->telefonos()->create(['numero'=>'6018088128']);


        $sede2->direccion()->create([
            'municipio_id'=>'11007',
            'direccion'=>'Tv 78L Nº 69C 10 sur'
        ]);

        $empleado = \App\Models\Empleado::create([
            'cargo'=>'Bacteriologa',
            'firma'=>'firma_claudia.png',
            'tipo_documento'=>'CC',
            'numero_documento'=>'51934571',
            'fecha_nacimiento'=>'1969-01-11',
            'user_id'=>2,
            'empresa_id'=>1,

        ]);
        $empleado->telefonos()->create([
            'numero'=>'3207001403'
        ]);

        $admon= \App\Models\Empleado::create([
            'cargo'=>'Administrador',
            'firma'=>'ramirez.png',
            'tipo_documento'=>'CC',
            'numero_documento'=>'80920160',
            'fecha_nacimiento'=>'1985-08-08',
            'user_id'=>1,
            'empresa_id'=>1,

        ]);
        $admon->telefonos()->create([
            'numero'=>'3014819820'

        ]);

        $coordi =  Persona::create([
            'primer_nombre'=>'Diana',
            'segundo_nombre'=>'Carolina',
            'primer_apellido'=>'Arboleda',
            'segundo_apellido'=>'Hernandez',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456',
            'fecha_nacimiento'=>'1989-11-12',
            'sexo'=>'F',
            'nacional'=>true,

        ]);
        $agente =  Persona::create([
            'primer_nombre'=>'Ronald',
            'primer_apellido'=>'McDonald',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456789',
            'fecha_nacimiento'=>'1929-01-11',
            'sexo'=>'M',
            'nacional'=>false,

        ]);

        $empleado2 = \App\Models\Empleado::create([

            'cargo'=>'contador',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456789',
            'fecha_nacimiento'=>'1929-01-11',
            'user_id'=>3,

            'empresa_id'=>1,
        ]);

        $empleado3 = \App\Models\Empleado::create([

            'cargo'=>'secretaria',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456',
            'fecha_nacimiento'=>'1989-11-12',
            'user_id'=>4,
            'empresa_id'=>1,

        ]);

        $empleado3->telefonos()->create([
            'numero'=>'350060000'

        ]);
        $empleado2->telefonos()->create([
            'numero'=>'310060000'

        ]);

        $empleado2->direccion()->create([
            'municipio_id'=>'11007',
            'direccion'=>'Calle 80 I sur # 81 J 36'
        ]);
        $empleado->sede()->associate($sede);
        $empleado->sede()->associate($sede2);
        $empleado2->sede()->associate($sede2);
        $empleado3->sede()->associate($sede2);


    }
}
