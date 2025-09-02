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
            $table->string('nombre', 255);
            $table->string('grupo', 255)->nullable();
            $table->boolean('activo')->default(false);
            $table->unsignedInteger('nivel')->default(1);
            $table->string('cod_sin_formato')->nullable();
            $table->timestamps();
            $table->primary('codigo');
            $table->softDeletes();
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
