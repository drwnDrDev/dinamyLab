<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_medicas', function (Blueprint $table) {
            $table->string('numero', 255)->change();
        });
    }

    public function down(): void
    {
        foreach (DB::table('ordenes_medicas')->select('numero')->whereNotNull('numero')->cursor() as $orden) {
            if (! ctype_digit((string) $orden->numero)) {
                throw new RuntimeException(
                    'No se puede revertir el tipo de numero: existen órdenes con identificadores no numéricos.'
                );
            }
        }

        Schema::table('ordenes_medicas', function (Blueprint $table) {
            $table->unsignedMediumInteger('numero')->change();
        });
    }
};
