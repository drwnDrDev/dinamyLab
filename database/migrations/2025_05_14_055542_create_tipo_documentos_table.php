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
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('cod_rips', 10)->unique();
            $table->string('cod_dian', 10)->nullable();
            $table->boolean('es_nacional')->default(true);
            $table->unsignedTinyInteger('edad_minima');
            $table->unsignedTinyInteger('edad_maxima');
            $table->unsignedTinyInteger('unidad_edad'); // 1: Años, 2: Meses, 3: Días
            $table->string('regex_validacion')->nullable(); // Expresión regular para validación
            $table->boolean('requiere_acudiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
    }
};
