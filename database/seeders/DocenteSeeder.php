<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Docente;
class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     Docente::create([
            'nombre'    => 'Maestro1',
            'apellidos' => 'Maestro',
            'email'     => 'maestro@gmail.com',
            'estatus'   => 'Activo'
     ]);
             Docente::create([
            'nombre'    => 'Brad',
            'apellidos' => 'Pitt',
            'email'     => 'brad@gmail.com',
            'estatus'   => 'Inactivo'
        ]);
        Docente::create([
            'nombre'    => 'Keanu',
            'apellidos' => 'Reeves',
            'email'     => 'prueba@gmail.com',
            'estatus'   => 'Activo'
        ]);
    }
}
