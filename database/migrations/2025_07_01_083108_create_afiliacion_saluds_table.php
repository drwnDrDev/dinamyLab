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
        Schema::create('afiliaciones_salud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')
                ->constrained('personas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('eps');
            $table->string('tipo_afiliacion')
                ->default('12') // Asegúrate de que '12' sea un valor válido en tipos_afiliaciones
                ->constrained('tipos_afiliaciones', 'codigo')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliaciones_salud');
    }
};
