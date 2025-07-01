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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->unsignedBigInteger('numero')->unique();
            $table->string('cufe', 64)->nullable();
            $table->timestamp('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_ajustes', 12, 2)->default(0);
            $table->foreignId('sede_id')->nullable()
                ->constrained('sedes')
                ->nullOnDelete();
            $table->foreignId('convenio_id')->nullable()
                ->constrained('convenios')
                ->nullOnDelete();
            $table->foreignId('empleado_id')->nullable()
                ->constrained('empleados')
                ->nullOnDelete();
            $table->foreignId('resolucion_id')->nullable()
                ->constrained('resoluciones')
                ->nullOnDelete(); 
            $table->table('qr')->nullable();
            $table->string('estado')->default('PENDIENTE');
            $table->string('tipo_pago')->default('CONTADO');
            $table->decimal('total', 12, 2);
            $table->string('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
