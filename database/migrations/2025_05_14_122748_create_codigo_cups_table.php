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
            $table->string('codigo', 10);
            $table->string('nombre', 100);
            $table->string('grupo', 100);
            $table->boolean('activo')->default(false);
            $table->unsignedTinyInteger('nivel')->default(1);
            $table->string('cod_sin_formato')->nullable();
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
