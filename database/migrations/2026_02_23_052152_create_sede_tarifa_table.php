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
        Schema::create('sede_tarifa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')
                ->constrained('sedes')
                ->onDelete('cascade');
            $table->foreignId('tarifa_id')
                ->constrained('tarifas')
                ->onDelete('cascade');

            $table->timestamps();
            $table->unique(['sede_id', 'tarifa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sede_tarifa');
    }
};
