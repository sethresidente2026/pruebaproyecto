<?php
namespace App\Services;
use App\Models\Espacio;

class EspacioService
{
     public function obtenerTodos()
    {
       return Espacio::all();
    }
    public function crear(array $datos)
    {
        return Espacio::create($datos);
    }
    public function actualizar(Espacio $espacio,array $datos){
        $espacio->update($datos);
        return $espacio;
    }
    public function eliminar(Espacio $espacio)
    {
         $espacio->delete();
         return true;
    }
}