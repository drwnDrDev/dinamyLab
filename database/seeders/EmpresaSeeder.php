<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Persona;
use App\Models\InformacionAdicional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contacto::create([
        'municipio_id'=>11007,
        'telefono'=>'NO REFIERE',
        ]);

        $extranjero = Contacto::create([
            'municipio_id'=>11007,
            'telefono'=>'+1 800 123 4567',

        ]);

        $ingoE=[
        new InformacionAdicional(    [
                'tipo'=>'pais',
                'valor'=>'USA'
            ]),
        new InformacionAdicional(    [
            'tipo'=>'direccion',
            'valor'=>'Calle 80 I sur # 81 J 36'
            ]),
        new InformacionAdicional(    [
            'tipo'=>'email',
            'valor'=>'ronaldmcdonalds@dinamycode.com',
            'liga'=>'mailto:ronaldmcdonalds@dinamycode.com'
            ]),
 
        ];

        $contacto = Contacto::create([
            'municipio_id'=>11007,
            'telefono'=>'3207001403'

        ]);

        $infoA1 = [
            new InformacionAdicional(
            [
            'tipo'=>'Google',
            'liga'=>'https://g.co/kgs/gkv1pHU',
            'valor'=>'Laboratorio Clínico Biotek',
            'descripcion'=>'Google Maps link to Biotek'
            ]),
          new InformacionAdicional(
            [
                'tipo'=>'Instagram',
                'valor'=>'@biotek_bosa',
                'liga'=>'https://www.instagram.com/biotek_bosa/',
                'descripcion'=>'Instagram profile of Biotek'
            ]),
          new InformacionAdicional(    
            [
                'tipo'=>'Twitter',
                'valor'=>'@biotek_bosa',
                'liga'=>'https://twitter.com/biotek_bosa',
                'descripcion'=>'Twitter profile of Biotek'
            ]),
          new InformacionAdicional(
            [
                'tipo'=>'Facebook',
                'liga'=>'https://www.facebook.com/search/top?q=biotek',
                'valor'=>'Biotek',
                'descripcion'=>'Facebook page of Biotek'
            ])
            ,
        new InformacionAdicional(    [
                'tipo'=>'Linkedin',
                'liga'=>'https://www.linkedin.com/company/dinamycode',
                'valor'=>'Dinamycode',
                'descripcion'=>'LinkedIn profile of Dinamycode'
            ]),
           new InformacionAdicional( [
                'tipo'=>'Whatsapp',
                'liga'=>'https://wa.me/573207001403',
                'valor'=>'3207001403',
                'descripcion'=>'WhatsApp contact for Biotek'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'email',
                'liga'=>'mailto:cpbuitrago69@yahoo.com',
                'valor'=>'cpbuitrago69@yahoo.com'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'direccion',
                'valor'=>'Diagonal 69 C sur # 78 C 36'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'pais',
                'valor'=>'COL'
            ])
        ];
    $contacto2 = Contacto::create([
            'municipio_id'=>11007,
            'telefono'=>'3005705987'
    ]);
    $contacto3 = Contacto::create([
            'municipio_id'=>25754,
            'telefono'=>'3014819820'
    ]);


  $infoA2 =[

           new InformacionAdicional( [
                'tipo'=>'direccion', 'valor'=>'Tv 78L Nº 69C 10 sur'
            ]),
          new InformacionAdicional(  [
                'tipo'=>'telefono',
                'valor'=>'3005705987'
            ]),
         new InformacionAdicional(        [
                'tipo'=>'telefono',
                'valor'=>'3103213025'
            ]),

       new InformacionAdicional(     [
                'tipo'=>'Google',
                'liga'=>'https://g.co/kgs/gkv1pHU',
                'valor'=>'Laboratorio Clínico Biotek',
                'descripcion'=>'Google Maps link to Biotek'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'Instagram',
                'valor'=>'@biotek_bosa',
                'liga'=>'https://www.instagram.com/biotek_bosa/',
                'descripcion'=>'Instagram profile of Biotek'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'Twitter',
                'valor'=>'@biotek_bosa',
                'liga'=>'https://twitter.com/biotek_bosa',
                'descripcion'=>'Twitter profile of Biotek'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'Facebook',
                'liga'=>'https://www.facebook.com/search/top?q=biotek',
                'valor'=>'Biotek',
                'descripcion'=>'Facebook page of Biotek'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'Linkedin',
                'liga'=>'https://www.linkedin.com/company/dinamycode',
                'valor'=>'Dinamycode',
                'descripcion'=>'LinkedIn profile of Dinamycode'
            ]),
        new InformacionAdicional(    [
                'tipo'=>'Whatsapp',
                'liga'=>'https://wa.me/573005705987',
                'valor'=>'3005705987',
                'descripcion'=>'WhatsApp contact for Biotek'
            ]),
        ];
    

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
            'contacto_id'=>$contacto->id,
        ]);

        $empresa = \App\Models\Empresa::create([
            'nit'=>'51934571-8',
            'razon_social'=>'Laboratorio Claudia Buitrago',
            'nombre_comercial'=>'Biotek',

            'contacto_id'=>$contacto->id,
        ]);
    $contacto->informacionAdicional()->saveMany($infoA1);
    $contacto2->informacionAdicional()->saveMany($infoA2);
    $extranjero->informacionAdicional()->saveMany($ingoE);
   
    $contacto3->informacionAdicional()->create([
            'tipo'=>'email',
            'valor'=>'rlcirilo@gmail.com'
        ]);

        $sede = \App\Models\Sede::create([
            'nombre'=>'Biotek Bosa',
            'codigo_prestador'=>'110010822701',
            'logo'=>'biotek_logo.png',
            'empresa_id'=>$empresa->id,
            'contacto_id'=>$contacto->id,
        ]);

        $sede2 = \App\Models\Sede::create([
            'nombre'=>'IPS Bosa',
            'codigo_prestador'=>'110010822703',
            'logo'=>'ryc.png',
            'empresa_id'=>$empresa->id,
            'contacto_id'=>$contacto2->id,
        ]);
        $empleado = \App\Models\Empleado::create([
            'cargo'=>'Bacteriologa',
            'firma'=>'firma_claudia.png',
            'tipo_documento'=>'CC',
            'numero_documento'=>'51934571',
            'fecha_nacimiento'=>'1969-01-11',
            'user_id'=>2,
            'empresa_id'=>1,
            'contacto_id'=>$contacto->id,
        ]);
        $admon= \App\Models\Empleado::create([
            'cargo'=>'Administrador',
            'firma'=>'ramirez.png',
            'tipo_documento'=>'CC',
            'numero_documento'=>'80920160',
            'fecha_nacimiento'=>'1985-08-08',
            'user_id'=>1,
            'empresa_id'=>1,
            'contacto_id'=>$contacto3->id,
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
            'contacto_id'=>1,
        ]);
        $agente =  Persona::create([
            'primer_nombre'=>'Ronald',
            'primer_apellido'=>'McDonald',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456789',
            'fecha_nacimiento'=>'1929-01-11',
            'sexo'=>'M',
            'nacional'=>false,

            'contacto_id'=>$extranjero->id,
        ]);

        $empleado2 = \App\Models\Empleado::create([
      
            'cargo'=>'contador',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456789',
            'fecha_nacimiento'=>'1929-01-11',
            'user_id'=>3,
            'contacto_id'=>$extranjero->id,
            'empresa_id'=>1,
        ]);

        $empleado3 = \App\Models\Empleado::create([
        
            'cargo'=>'secretaria',
            'tipo_documento'=>'CC',
            'numero_documento'=>'123456',
            'fecha_nacimiento'=>'1989-11-12',
            'user_id'=>4,
            'empresa_id'=>1,
            'contacto_id'=>1,
        ]);


    }
}
