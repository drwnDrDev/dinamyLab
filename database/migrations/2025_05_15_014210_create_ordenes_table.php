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
        Schema::create('ordenes_medicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('numero');
            $table->foreignId('paciente_id')->constrained('personas')->onDelete('cascade');
            $table->unsignedBigInteger('acomanhante_id')->nullable();
            $table->foreign('acomanhante_id')->references('id')->on('personas')->onDelete('cascade');
            $table->string('descripcion')->nullable();
            $table->decimal('abono', 10, 2)->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_medicas');
    }
};
