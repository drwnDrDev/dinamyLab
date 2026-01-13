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
        Schema::create('pre_registros_citas', function (Blueprint $table) {
            $table->id();

            // Datos básicos proporcionados por usuario
            $table->string('nombres_completos'); // Nombre como lo escribe el usuario
            $table->string('numero_documento')->nullable(); // Puede no tenerlo a mano
            $table->string('tipo_documento')->default('CC');
            $table->string('telefono_contacto')->nullable();
            $table->string('email')->nullable();

            // Información de la cita
            $table->date('fecha_deseada')->nullable();
            $table->time('hora_deseada')->nullable();
            $table->text('motivo')->nullable(); // Razón de la consulta
            $table->text('observaciones')->nullable();

            // Estado del pre-registro
            $table->enum('estado', [
                'pendiente',      // Recién creado por usuario
                'confirmado',     // Recepción verificó y completó datos
                'cancelado',      // Usuario o recepción canceló
                'atendido'        // Ya se atendió la cita
            ])->default('pendiente');

            // Relación con persona formal (cuando recepción completa)
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();

            // Relación con orden (si se crea después de confirmar)
            $table->foreignId('orden_id')->nullable()->constrained('ordenes_medicas')->nullOnDelete();

            // Metadatos
            $table->string('codigo_confirmacion')->unique()->nullable(); // Para que usuario pueda consultar
            $table->timestamp('fecha_confirmacion')->nullable(); // Cuando recepción confirma
            $table->foreignId('confirmado_por')->nullable()->constrained('users')->nullOnDelete(); // Usuario que confirmó
            $table->json('datos_parseados')->nullable(); // Guarda el parsing automático

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('numero_documento');
            $table->index('estado');
            $table->index('fecha_deseada');
            $table->index('codigo_confirmacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_registros_citas');
    }
};
