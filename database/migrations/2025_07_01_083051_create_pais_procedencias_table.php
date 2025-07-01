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
        Schema::create('pais_procedencias', function (Blueprint $table) {
            $table->id();
            $table->string('pais_codigo_iso'); // Columna para almacenar el código ISO del país
            $table->foreign('pais_codigo_iso')
                  ->references('codigo_iso')
                  ->on('paises')
                  ->onUpdate('cascade');
            $table->morphs('procedencia'); // Polymorphic relation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pais_procedencias');
    }
};
