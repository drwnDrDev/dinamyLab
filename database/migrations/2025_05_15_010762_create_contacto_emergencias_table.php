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
        Schema::create('contacto_emergencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')
                ->constrained('personas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('acompanante_id')
                ->constrained('personas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('parentesco'); // Ejemplo: Madre, Padre, Hermano, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacto_emergencias');
    }
};
