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
        Schema::create('tributos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impuesto_id')
                ->constrained('impuestos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('factura_id')
                ->constrained('facturas')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->decimal('base', 12, 2);
            $table->decimal('porcentaje', 5, 2);
            $table->decimal('exento', 10, 2)->nullable();
            $table->decimal('retencion', 10, 2)->nullable();

            $table->decimal('valor', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tributos');
    }
};
