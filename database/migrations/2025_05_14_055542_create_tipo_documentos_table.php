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
            $table->boolean('es_paciente')->default(true); // Se puede usar para filtrar tipos de documento solo para pacientes
            $table->boolean('es_pagador')->default(false); // Solo para pagadores (DIAN)
            $table->boolean('requiere_acudiente')->default(false);
            $table->unsignedTinyInteger('edad_minima')->default(0); // Edad mínima para este tipo de documento
            $table->unsignedTinyInteger('edad_maxima')->default(130);
            $table->string('regex_validacion')->nullable(); // Expresión regular para validación
            $table->unsignedTinyInteger('nivel')->default(0); // Nivel de importancia o jerarquía del tipo de documento
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
