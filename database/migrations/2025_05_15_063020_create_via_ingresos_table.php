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
        Schema::create('vias_ingreso', function (Blueprint $table) {
            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->unsignedTinyInteger('nivel')->default(1);
            $table->boolean('activo')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vias_ingreso');
    }
};
