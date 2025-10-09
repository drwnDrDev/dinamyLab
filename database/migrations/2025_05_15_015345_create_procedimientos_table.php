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
            $table->string('diagnostico_principal',10);
            $table->unsignedInteger('codigo_servicio')->default(706); //Laboratorio clinico
            $table->foreign('codigo_servicio')
                ->references('codigo')
                ->on('servicios_habilitados')
                ->onDelete('cascade');
            $table->string('diagnostico_relacionado',10)->nullable();
            $table->foreign('diagnostico_principal')
                    ->references('codigo')
                    ->on('codigo_diagnosticos')
                    ->onDelete('cascade');
            $table->foreign('diagnostico_relacionado')
                    ->references('codigo')
                    ->on('codigo_diagnosticos')
                    ->onDelete('cascade');
            $table->string('codigo_finalidad');
            $table->foreign('codigo_finalidad')
                ->references('codigo')
                ->on('finalidades')
                ->onDelete('cascade');
            $table->string('codigo_modalidad');
            $table->foreign('codigo_modalidad')
                ->references('codigo')
                ->on('modalidades')
                ->onDelete('cascade');
            $table->string('codigo_via_ingreso');
            $table->foreign('codigo_via_ingreso')
                ->references('codigo')
                ->on('vias_ingreso')
                ->onDelete('cascade');
            $table->string('estado')->default('pendiente'); // Estado del procedimiento
            $table->timestamps();
            $table->softDeletes();
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
