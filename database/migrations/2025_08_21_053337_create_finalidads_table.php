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
        Schema::create('finalidades', function (Blueprint $table) {
            $table->string('codigo')->primary();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->unsignedTinyInteger('nivel')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finalidades');
    }
};
