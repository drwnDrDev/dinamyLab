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
        Schema::create('cup_diagnostico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codigo_cup')
                ->constrained('codigo_cups','id')
                ->onDelete('cascade');
            $table->foreignId('codigo_diagnostico')
                ->constrained('codigo_diagnosticos','id')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('nivel')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cup_diagnostico');
    }
};
