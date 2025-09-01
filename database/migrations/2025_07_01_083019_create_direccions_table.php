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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->string('direccion')->nullable();
            $table->morphs('direccionable'); // Polymorphic relation
            $table->foreignId('municipio_id')
                ->nullable()
                ->constrained('municipios')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('pais_id', 3)
                ->nullable()
                ->constrained('paises', 'codigo_iso')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('codigo_postal', 10)->nullable();
            $table->boolean('rural')->default(false);
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
