<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Las órdenes conservan el nombre, destino y valores históricos.
        Schema::table('orden_remisiones', function (Blueprint $table) {
            $table->foreignId('valor_remision_id')->nullable()->constrained('valores_remision')->nullOnDelete();
            $table->foreignId('ips_externa_id')->nullable()->constrained('ips_externas')->nullOnDelete();
            $table->decimal('valor_remitido', 10, 2)->nullable();
        });

        // También actualiza instalaciones que ya ejecutaron la migración original.
        if (Schema::hasColumn('orden_remisiones', 'destino_sede_id')) {
            Schema::table('orden_remisiones', fn (Blueprint $table) => $table->dropConstrainedForeignId('destino_sede_id'));
        }
    }

    public function down(): void
    {
        Schema::table('orden_remisiones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('valor_remision_id');
            $table->dropConstrainedForeignId('ips_externa_id');
            $table->dropColumn('valor_remitido');
            $table->foreignId('destino_sede_id')->nullable()->constrained('sedes')->nullOnDelete();
        });
    }
};
