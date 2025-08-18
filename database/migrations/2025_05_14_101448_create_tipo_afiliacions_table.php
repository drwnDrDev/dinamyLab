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
        Schema::create('tipos_afiliaciones', function (Blueprint $table) {
            $table->string('codigo')->primary();
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedTinyInteger('nivel')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_afiliaciones');
    }
};
