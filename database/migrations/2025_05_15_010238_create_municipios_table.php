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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('departamento');
            $codigo->string('codigo_departamento');
            $table->string('municipio');
            $table->string('codigo_municipio');
            $table->string('indicativo_telefono')->nullable();
            $table->unsignedMediumInteger('nivel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
