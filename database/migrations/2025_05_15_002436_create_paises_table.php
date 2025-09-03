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
        Schema::create('paises', function (Blueprint $table) {
            $table->string('codigo_iso',3)->primary();
            $table->string('codigo_iso_3', 3)->unique();
            $table->string('codigo_iso_2', 2)->unique();
            $table->string('nombre')->unique();
            $table->unsignedSmallInteger('nivel')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pais');
    }
};
