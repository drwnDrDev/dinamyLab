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
        Schema::create('ordenes_medicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            $table->unsignedMediumInteger('numero');
            $table->string('via_ingreso')->default('01'); //demanda espontanea
            $table->string('modalidad_atentcion')->default('01'); //intramural
            $table->string('tipo_atencion')->default('01'); //primera vez
            $table->unsignedInteger('codigo_servicio')->default(706); //Laboratorio clinico
            $table->foreign('codigo_servicio')
                ->references('codigo')
                ->on('servicios_habilitados')
                ->onDelete('cascade');
            $table->foreignId('paciente_id')
                ->constrained('personas')
                ->onDelete('cascade');
            $table->string('codigo_recaudo')->default('05');
            $table->string('observaciones')->nullable();
            $table->timestamp('terminada')->nullable();
            $table->decimal('abono', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_medicas');
    }
};

