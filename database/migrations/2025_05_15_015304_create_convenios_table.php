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
        Schema::create('convenios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')
            ->constrained('tipo_documentos');
            $table->string('numero_documento')->unique();
            $table->string('razon_social');
            $table->string('numero_contrato')->nullable();
            $table->string('modalidad_pago', 42)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios');
    }
};
