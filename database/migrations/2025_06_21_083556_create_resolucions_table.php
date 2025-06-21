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
        Schema::create('resoluciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 120)->unique();
            $table->string('prefijo', 10)->nullable();
            $table->string('res_facturacion')->nullable();
            $table->bigInteger('incio_facturacion')->nullable();
            $table->bigInteger('fin_facturacion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('activa')->default(true);
            $table->foreignId('empresa_id')
                    ->constrained('empresas')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolucions');
    }
};
