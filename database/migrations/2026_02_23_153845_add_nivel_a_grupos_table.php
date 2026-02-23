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
        Schema::table('grupos', function (Blueprint $table) {
            $table->enum('nivel', [
                'Preescolar', 
                'Primaria', 
                'Secundaria', 
                'Bachillerato', 
                'Licenciatura', 
                'Mixto'
            ])->default('Mixto')->after('nombre'); // 'nombre' o como se llame la columna principal de tu grupo
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('nivel');
        });
    }
};
