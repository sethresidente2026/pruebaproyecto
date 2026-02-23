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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            // Relación con el grupo y el maestro titular
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
            
            // Relación opcional para cuando hay una sustitución
            $table->foreignId('docente_sustituto_id')->nullable()->constrained('docentes')->onDelete('set null');
            
            // Datos del pase de lista
            $table->date('fecha');
            $table->enum('estado', ['Asistió', 'Falta', 'Retardo', 'Sustitución'])->default('Asistió');
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
