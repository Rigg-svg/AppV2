<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->enum('estado', ['programada', 'cancelada', 'completada'])->default('programada');
            $table->text('motivo')->nullable();
            $table->text('notas')->nullable();
            $table->unsignedSmallInteger('duracion_minutos')->default(30);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha', 'hora']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};