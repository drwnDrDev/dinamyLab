<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Orden;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orden_remisiones', function (Blueprint $table) {
            $table->string('estado')->default('pendiente de muestra')->index();
        });
        Orden::query()->chunkById(100, fn ($ordenes) => $ordenes->each->sincronizarEstado());
    }

    public function down(): void
    {
        Schema::table('orden_remisiones', fn (Blueprint $table) => $table->dropColumn('estado'));
    }
};
