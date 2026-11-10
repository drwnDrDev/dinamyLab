<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normaliza personas.sexo a los códigos canónicos F/M/I y agrega la FK
     * hacia sexos.codigo solo en motores que la soportan (SQLite se omite).
     */
    public function up(): void
    {
        if (! Schema::hasTable('personas') || ! Schema::hasTable('sexos')) {
            return;
        }

        DB::table('personas')->where('sexo', 'H')->update(['sexo' => 'M']);
        DB::table('personas')
            ->whereNotNull('sexo')
            ->whereNotIn('sexo', ['F', 'M', 'I'])
            ->update(['sexo' => 'I']);

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        if ($this->foreignKeyExists()) {
            return;
        }

        try {
            Schema::table('personas', function (Blueprint $table) {
                $table->foreign('sexo')
                    ->references('codigo')
                    ->on('sexos')
                    ->restrictOnDelete();
            });
        } catch (\Throwable $e) {
            // La migración debe poder ejecutarse dos veces incluso en motores
            // donde la introspección de llaves foráneas no está implementada.
            if (! str_contains(strtolower($e->getMessage()), 'duplicate')) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('personas') || DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        try {
            Schema::table('personas', function (Blueprint $table) {
                $table->dropForeign(['sexo']);
            });
        } catch (\Throwable $e) {
            // Si la FK no existe, no hay nada que revertir.
        }
    }

    private function foreignKeyExists(): bool
    {
        $driver = DB::connection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return false;
        }

        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'personas')
            ->where('COLUMN_NAME', 'sexo')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();
    }
};
