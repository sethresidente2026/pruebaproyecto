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
    Schema::create('grupos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100);
        $table->integer('cupo_maximo');

        // IMPORTANTE: Fíjate que dentro de constrained() esté el nombre en español
        $table->foreignId('actividad_id')->constrained('actividades');       // Coincide con tu código nuevo
        $table->foreignId('docente_id')->constrained('docentes');            // Coincide con tabla docentes
        $table->foreignId('ciclo_id')->constrained('ciclos_escolares');      // Coincide con tu código nuevo
        $table->foreignId('nivel_id')->nullable()->constrained('niveles');   // Coincide con tu código nuevo

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
