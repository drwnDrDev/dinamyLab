<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_remisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_medicas')->cascadeOnDelete();
            $table->string('cup', 20);
            $table->string('nombre');
            $table->decimal('valor', 10, 2);
            $table->foreignId('destino_sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('destino');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_remisiones');
    }
};
