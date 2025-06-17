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
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->string('resultado');
            $table->boolean('es_normal')->default(true);
            $table->string('valor_referencia')->nullable();
            $table->foreignId('parametro_id')->constrained('parametros');
            $table->foreignId('procedimiento_id')->constrained('procedimientos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
