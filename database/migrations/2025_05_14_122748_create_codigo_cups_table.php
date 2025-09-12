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
        Schema::create('codigo_cups', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 255);
            $table->string('grupo', 255)->nullable();
            $table->boolean('activo')->default(false);
            $table->unsignedInteger('nivel')->default(1);
            $table->string('cod_sin_formato')->nullable();
            $table->string('sexo_aplicable', 1)->default('A');
            $table->unsignedTinyInteger('edad_minima')->default(0);
            $table->unsignedTinyInteger('edad_maxima')->default(255);
            $table->boolean('qx')->default(false);
            $table->string('cobertura')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_cups');
    }
};
