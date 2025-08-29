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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->foreignId('tipo_documento_id')
                ->constrained('tipo_documentos')
                ->cascadeOnUpdate();
            $table->string('numero_documento')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo',['F','M'])->nullable();
            $table->string('pais_origen', 3)
                ->nullable()
                ->constrained('paises', 'codigo_iso')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
