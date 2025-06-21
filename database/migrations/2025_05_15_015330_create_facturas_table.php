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
            $table->unsignedBigInteger('numero')->unique();
            $table->string('cufe', 64)->unique();
            $table->date('fecha_emision');
            $table->morphs('pagador');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_ajustes', 12, 2)->default(0);
            $table->foreignId('impuestos_id')->nullable()
                ->constrained('impuestos')
                ->nullOnDelete();
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
