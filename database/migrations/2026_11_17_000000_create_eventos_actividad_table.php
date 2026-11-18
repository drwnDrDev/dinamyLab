<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_actividad', function (Blueprint $table) {
            $table->id();
            // Sin cascadas: el historial sobrevive a la eliminación del usuario o recurso.
            $table->unsignedBigInteger('usuario_id')->nullable()->index();
            $table->string('usuario_nombre');
            $table->string('evento', 80)->index();
            $table->string('recurso_tipo', 80)->nullable();
            $table->unsignedBigInteger('recurso_id')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable()->index();
            $table->unsignedBigInteger('sede_id')->nullable()->index();
            $table->json('detalles')->nullable();
            $table->dateTime('ocurrio_en')->index();
            $table->index(['recurso_tipo', 'recurso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_actividad');
    }
};
