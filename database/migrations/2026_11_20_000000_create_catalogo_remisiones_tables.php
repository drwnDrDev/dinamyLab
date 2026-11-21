<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remisiones', function (Blueprint $table) {
            $table->id();
            $table->string('cup', 20)->unique();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('ips_externas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('nit')->nullable();
            $table->string('telefono')->nullable();
            $table->timestamps();
        });

        Schema::create('valores_remision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remision_id')->constrained('remisiones')->cascadeOnDelete();
            $table->foreignId('ips_externa_id')->constrained('ips_externas')->cascadeOnDelete();
            $table->decimal('precio', 10, 2);
            $table->decimal('valor_remitido', 10, 2)->nullable();
            $table->timestamps();
            $table->unique(['remision_id', 'ips_externa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valores_remision');
        Schema::dropIfExists('ips_externas');
        Schema::dropIfExists('remisiones');
    }
};
