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
        Schema::create('informacion_adicional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contacto_id')
                ->constrained('contactos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('nombre_tipo')->nullable();
            $table->string('valor')->nullable();
            $table->string('liga')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_adicionals');
    }
};
