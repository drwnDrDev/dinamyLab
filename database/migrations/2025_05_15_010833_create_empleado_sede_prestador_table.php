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
        Schema::create('empleado_sede_prestador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('sede_id')
                ->constrained('sedes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('prestador');
            $table->string('estado', 20)->default('activo'); // Estado del empleado
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_sede_prestador');
    }
};
