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

    $table->foreignId('sede_id')
        ->constrained('sedes')
        ->restrictOnDelete()
        ->restrictOnUpdate();

    $table->foreignId('empleado_id')
        ->constrained('empleados')
        ->restrictOnDelete()
        ->restrictOnUpdate();

    $table->foreignId('resolucion_id')
        ->constrained('resoluciones')
        ->restrictOnDelete()
        ->restrictOnUpdate();

    $table->morphs('pagador'); // Relación polimórfica
    $table->string('qr')->nullable();
    $table->string('estado')->default('PENDIENTE');

    $table->decimal('subtotal', 12, 2);

    $table->decimal('total_ajustes', 12, 2)->default(0);
    $table->decimal('total', 12, 2);
    $table->string('observaciones')->nullable();
    $table->timestamps();
    $table->softDeletes();
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
