<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('codigo_diagnosticos', function (Blueprint $table) {
            $table->string('codigo', 10)->primary();
            $table->string('nombre', 255);
            $table->string('descripcion');
            $table->string('grupo', 255)->nullable();
            $table->unsignedTinyInteger('sub_grupo')->nullable();
            $table->boolean('activo')->default(false);
            $table->unsignedTinyInteger('edad_minima')->default(0);
            $table->unsignedTinyInteger('edad_maxima')->default(255);
            $table->string('sexo_aplicable',1)->default('A');
            $table->string('grupo_mortalidad')->nullable();
            $table->string('capitulo')->nullable();
            $table->unsignedInteger('nivel')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_diagnosticos');
    }
};
