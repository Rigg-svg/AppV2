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
        Schema::table('medicos', function (Blueprint $table) {
            $table->time('hora_inicio_jornada')->default('08:00:00');
            $table->time('hora_fin_jornada')->default('17:00:00');
            $table->unsignedSmallInteger('duracion_cita_minutos')->default(30);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicos', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio_jornada', 'hora_fin_jornada', 'duracion_cita_minutos']);
        });
    }
};
