<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CatalogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('niveles')->insert([
        
        ['nombre' => 'Licenciatura'],
        ['nombre' => 'Maestría'],
        ['nombre' => 'Bachillerato'],
        ['nombre' => 'Secundaria'],
        ['nombre' => 'Primaria'],
        ]);
        
        DB::table('ciclos_escolares')->insert([
            ['nombre' => '2025-2026 Semestre A', 'activo' => 1],
            ['nombre' => '2025-2026 Semestre B', 'activo' => 0],
            ['nombre' => '2027-2028 Semestre A', 'activo' => 0],
            ['nombre' => '2028-2029 Semestre B', 'activo' => 0],
        ]);
        DB::table('actividades')->insert([
            ['nombre' => 'Danza Folklórica'], 
            ['nombre' => 'Fútbol'], 
            ['nombre' => 'Ajedrez'], 
        ]);
       
    }
}
