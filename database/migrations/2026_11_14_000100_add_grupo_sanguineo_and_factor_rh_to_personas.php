<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->string('grupo_sanguineo', 2)->nullable();
            $table->string('factor_rh', 8)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn(['grupo_sanguineo', 'factor_rh']);
        });
    }
};
