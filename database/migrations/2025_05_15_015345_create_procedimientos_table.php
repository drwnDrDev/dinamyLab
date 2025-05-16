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
                ->constrained('empleados')
                ->onDelete('cascade');
            $table->json('resultados');
            $table->string('observacion')->nullable();
            $table->foreignId('factura_id')
                ->nullable()
                ->constrained('facturas')
                ->onDelete('cascade');
            $table->date('fecha');
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
