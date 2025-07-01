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
        Schema::create('afiliacion_saluds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')
                ->constrained('personas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->sstring('eps');
            $table->string('tipo_afiliacion'); // Ejemplo: EPS, Prepagada, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliacion_saluds');
    }
};
