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
        Schema::create('examen_parametro', function (Blueprint $table) {

        $table->foreignId('examen_id')->constrained('examenes')->onDelete('cascade');
        $table->foreignId('parametro_id')->constrained()->onDelete('cascade');
        $table->primary(['examen_id', 'parametro_id']);


            $table->timestamps(); // Para created_at y updated_at de la relación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_parametro');
    }
};
