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
        Schema::create('factura_medio_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')
                ->constrained('facturas')
                ->onDelete('cascade');
            $table->foreignId('metodo_pago_id')
                ->constrained('metodo_pagos')
                ->onDelete('cascade');
            $table->decimal('monto', 12, 2); // Monto pagado con este método
            $table->string('referencia')->nullable(); // Referencia del pago, si aplica
            $table->timestamp('fecha_pago')->nullable(); // Fecha en que se realizó el pago
            $table->string('estado')->default('PENDIENTE'); // Estado del pago (PENDIENTE, COMPLETADO, CANCELADO, etc.)
            $table->string('observaciones')->nullable();
            $table->string('nombre_cuenta')->nullable(); // Nombre de la cuenta asociada (Caja,Nombre del banco,billetera,medio) al pago, si aplica
            $table->string('codigo_autorizacion')->nullable(); // Código de autorización del pago
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_medio_pagos');
    }
};
