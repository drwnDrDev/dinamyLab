<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Persona;
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
        'municipio_id'=>Municipio::where('codigo','11007')->first()->id,
        'telefono'=>'NO REFIERE',

        ]);
        $contacto = Contacto::create([
            'municipio_id'=>Municipio::where('codigo','11007')->first()->id,
            'telefono'=>'3207001403',
            'info_adicional'=>json_encode([
            'redes'=>[
                    "Google"=>'https://g.co/kgs/gkv1pHU',
                    "Facebook"=>'https://www.facebook.com/search/top?q=biotek',
                    "Linkedin"=>'https://www.linkedin.com/company/dinamycode',
                    "Whatsapp"=>'https://wa.me/573207001403',
                ],
            'email'=>'cpbuitrago69@yahoo.com',
            'direccion'=>'Diagonal 69 C sur # 78 C 36',
            'pais'=>'COL',
            ])
        ]);



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
            'razon_social'=>'Claudia Patricia Buitrago Hernandez',
            'nombre_comercial'=>'Biotek',
            'contacto_id'=>$contacto->id,
        ]);

        //     $table->string('nombre')->unique();
        //     $table->string('res_facturacion')->nullable();
        //     $table->bigInteger('incio_facturacion')->nullable();
        //     $table->bigInteger('fin_facturacion')->nullable();
        //     $table->foreignId('empresa_id')
        //         ->constrained('empresas')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->foreignId('contacto_id')
        //         ->constrained('contactos')
        //         ->nullOnDelete()
        //         ->cascadeOnUpdate();


        $sede = \App\Models\Sede::create([
            'nombre'=>'Biotek Bosa',
            'res_facturacion'=>'ASD234164416',
            'incio_facturacion'=>200000,
            'fin_facturacion'=>2999999,
            'empresa_id'=>$empresa->id,
            'contacto_id'=>$contacto->id,
        ]);

        $empleado = \App\Models\Empleado::create([
            'codigo'=>'110010822701',
            'cargo'=>'Bacteriologa',
            'persona_id'=>$persona->id,
            'user_id'=>2,
            'sede_id'=>1,
        ]);

    }
}
