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
        Schema::create('procedimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')
                ->constrained('ordenes_medicas')
                ->onDelete('cascade');
            $table->foreignId('empleado_id')
                ->nullable()
                ->constrained('empleados')
                ->onDelete('cascade');
            $table->foreignId('examen_id')
                ->constrained('examenes')
                ->onDelete('cascade');
            $table->foreignId('factura_id')
                ->nullable()
                ->constrained('facturas')
                ->onDelete('cascade');
            $table->date('fecha');
            $table->foreignId('sede_id')
                ->constrained('sedes')
                ->onDelete('cascade');
            $table->foreignId('contacto_emergencia_id')
                ->nullable()
                ->constrained('contacto_emergencias')
                ->onDelete('cascade');
            $table->string('estado')->default('pendiente'); // Estado del procedimiento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimientos');
    }
};
