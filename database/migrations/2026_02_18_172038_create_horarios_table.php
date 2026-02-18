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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('espacio_id')->constrained('espacios');
            $table->string('dia_semana',20);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
            $table->unique(['espacio_id', 'dia_semana', 'hora_inicio', 'hora_fin'], 'unique_espacio_horario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
