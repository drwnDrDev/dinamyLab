<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_anulacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedimiento_id')->constrained('procedimientos')->restrictOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->restrictOnDelete();
            $table->foreignId('solicitante_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo');
            $table->string('estado', 20)->default('pendiente');
            $table->foreignId('resuelta_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('respuesta')->nullable();
            $table->timestamp('resuelta_at')->nullable();
            $table->timestamps();
            $table->index(['procedimiento_id', 'estado']);
            $table->index(['sede_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_anulacion');
    }
};
