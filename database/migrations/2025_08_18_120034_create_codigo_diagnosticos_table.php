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
            $table->id();
            $table->string('codigo', 10);
            $table->string('nombre', 100);
            $table->string('descripcion');
            $table->string('grupo', 100)->nullable();
            $table->unsignedTinyInteger('sub_grupo')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedTinyInteger('edad_minima')->nullable();
            $table->unsignedTinyInteger('edad_maxima')->nullable();
            $table->string('sexo_aplicable',1)->default('A');
            $table->string('grupo_mortalidad')->nullable();
            $table->string('capitulo')->nullable();
            $table->unsignedTinyInteger('nivel')->default(1);
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
