<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea el catálogo canónico de sexos y lo siembra de forma idempotente.
     */
    public function up(): void
    {
        if (! Schema::hasTable('sexos')) {
            Schema::create('sexos', function (Blueprint $table) {
                $table->string('codigo', 1)->primary();
                $table->string('nombre');
                $table->string('cod_sexo', 2);
                $table->timestamps();
            });
        }

        $ahora = now();

        foreach ([
            ['codigo' => 'F', 'nombre' => 'Femenino', 'cod_sexo' => '02'],
            ['codigo' => 'I', 'nombre' => 'Indeterminado o Intersexual', 'cod_sexo' => '03'],
            ['codigo' => 'M', 'nombre' => 'Masculino', 'cod_sexo' => '01'],
        ] as $sexo) {
            DB::table('sexos')->updateOrInsert(
                ['codigo' => $sexo['codigo']],
                [
                    'nombre' => $sexo['nombre'],
                    'cod_sexo' => $sexo['cod_sexo'],
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sexos');
    }
};
