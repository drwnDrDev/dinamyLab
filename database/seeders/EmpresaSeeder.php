<?php

namespace Database\Seeders;


use App\Models\Persona;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



    $persona = Persona::create([
            // Datos de demostración.
            'grupo_sanguineo' => 'O',
            'factor_rh' => 'positivo',
            'primer_nombre'=>'Claudia',
            'segundo_nombre'=>'Patricia',
            'primer_apellido'=>'Buitrago',
            'segundo_apellido'=>'Hernandez',
            'tipo_documento_id' => \App\Models\TipoDocumento::idPorCodigoRips('CC'),
            'numero_documento'=>'51934571',
            'fecha_nacimiento'=>'1969-01-11',
            'sexo'=>'M',
            'pais_origen'=>'170'
        ]);
  


        $persona->direccion()->create([
            'municipio_id'=>'11001',
            'direccion'=>'Diagonal 69 C sur # 78 C 36'
        ]);
        $persona->telefonos()->create([
            'numero'=>'3207001403',
        ]);

        $persona->redesSociales()->create([
            'nombre'=>'Linkedin',
            'url'=>'https://www.linkedin.com/in/claudia-buitrago-hernandez-123456789/',
            'perfil'=>'Claudia Buitrago Hernandez',
        ]);

        $empresa = \App\Models\Empresa::create([
            'nit'=>'51934571-8',
            'razon_social'=>'LABORATORIO CLINICO CLAUDIA PATRICIA BUITRAGO HERNANDEZ',
            'nombre_comercial'=>'Biotek',

        ]);
   

        $empresa->telefonos()->create([
            'numero'=>'3207001403'
        ]);

        $empresa->direccion()->create([
            'municipio_id'=>'11001',
            'direccion'=>'Diagonal 69 C sur # 78 C 36'
        ]);

    

        $sede = \App\Models\Sede::create([
            'nombre'=>'Laboratorio',
            'codigo_prestador'=>'110010822701',
            'logo'=>'014.png',
            'empresa_id'=>$empresa->id,

        ]);
        $sede->telefonos()->create([
            'numero'=>'3207001403'
        ]);
        $sede->direccion()->create([
            'municipio_id'=>'11001',
            'direccion'=>'Diagonal 69 C sur # 78 C 36'
        ]);

        $sede2 = \App\Models\Sede::create([
            'nombre'=>'Toma de muestras',
            'codigo_prestador'=>'110010822703',
            'logo'=>'015.png',
            'empresa_id'=>$empresa->id,

        ]);



        $sede2->direccion()->create([
            'municipio_id'=>'11001',
            'direccion'=>'Tv 78L Nº 69C 10 sur'
        ]);

  

        $empleado = \App\Models\Empleado::create([
            'cargo'=>'Bacteriologa',
            'firma'=>'firma_claudia.png',
            'tipo_documento_id' => \App\Models\TipoDocumento::idPorCodigoRips('CC'),
            'numero_documento'=>'51934571',
            'fecha_nacimiento'=>'1969-01-11',
            'user_id'=>user::where('email', 'cpbuitrago69@yahoo.com')->first()->id,
            'empresa_id'=>1,

        ]);


        $empleado->telefonos()->create([
            'numero'=>'3207001403'
        ]);


 

        $admon= \App\Models\Empleado::create([
            'cargo'=>'Administrador de sistema',   
            'tipo_documento_id' => \App\Models\TipoDocumento::idPorCodigoRips('CC'),
            'numero_documento'=>'80920160',
            'fecha_nacimiento'=>'1985-08-08',
            'user_id'=>user::where('email', 'admon@dinamycode.com')->first()->id,
            'empresa_id'=>1,

        ]);
        $admon->telefonos()->create([
            'numero'=>'3014819820'

        ]);

 

        $empleado2 = \App\Models\Empleado::create([

            'cargo'=>'secretaria',
            'tipo_documento_id' => \App\Models\TipoDocumento::idPorCodigoRips('CC'),
            'numero_documento'=>'123456789',
            'fecha_nacimiento'=>'1929-01-11',
            'user_id'=>user::where('email', 'coordinador1@dinamycode.com')->first()->id,
            'empresa_id'=>1,
        ]);

        $empleado3 = \App\Models\Empleado::create([

            'cargo'=>'secretaria',
            'tipo_documento_id' => \App\Models\TipoDocumento::idPorCodigoRips('CC'),
            'numero_documento'=>'123456',
            'fecha_nacimiento'=>'1989-11-12',
            'user_id'=>user::where('email', 'coordinador2@dinamycode.com')->first()->id,
            'empresa_id'=>1,

        ]);


     
        // Relacionar empleados y sedes por tabla intermedia (muchos a muchos)
        $empleado2->sedes()->attach([$sede2->id]);
        $empleado->sedes()->attach([$sede->id, $sede2->id]);
        $empleado3->sedes()->attach($sede2->id);
        $admon->sedes()->attach([$sede2->id, $sede->id]);
        

    }
}
