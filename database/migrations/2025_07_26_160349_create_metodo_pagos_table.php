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
        Schema::create('metodos_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('codigo', 20)->unique(); // Código único para identificar el método de pago
            $table->string('descripcion')->nullable();
            $table->string('icono')->nullable(); // Ruta al icono del método de pago
            $table->boolean('activo')->default(true); // Indica si el método de
            $table->unsignedTinyInteger('nivel')->default(0); // Prioridad del método de pago, 1 es el más alto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pagos');
    }
};
