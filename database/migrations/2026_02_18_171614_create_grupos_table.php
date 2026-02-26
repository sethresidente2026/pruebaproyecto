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

       
        $table->foreignId('actividad_id')->constrained('actividades');       
        $table->foreignId('docente_id')->constrained('docentes');           
        $table->foreignId('ciclo_id')->constrained('ciclos_escolares');      
        $table->foreignId('nivel_id')->nullable()->constrained('niveles');   

        $table->timestamps();
        $table->index(['ciclo_id', 'docente_id']);
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
