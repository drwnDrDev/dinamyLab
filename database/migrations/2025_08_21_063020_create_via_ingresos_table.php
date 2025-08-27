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
        Schema::create('via_ingresos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedTinyInteger('nivel')->default(1);
            $table->boolean('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('via_ingresos');
    }
};
