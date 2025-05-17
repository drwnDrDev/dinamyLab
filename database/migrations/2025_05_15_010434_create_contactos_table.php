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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')
                ->constrained('municipios')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('pais_id')
                ->constrained('paises')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('telefono')->nullable();
            $table->json('info_adicional')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
