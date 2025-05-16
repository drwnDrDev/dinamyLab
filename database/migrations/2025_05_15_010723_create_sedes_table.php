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
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('res_facturacion')->nullable();
            $table->bigInteger('incio_facturacion')->nullable();
            $table->bigInteger('fin_facturacion')->nullable();
            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('contacto_id')
                ->constrained('contactos')
                ->nullOnDelete()
                ->cascadeOnUpdate();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
