<?php

namespace App\Services;

use App\Models\Actividad;
use Exception;

class ActividadService
{
    public function obtenerTodos()
    {
        return Actividad::orderBy('nombre', 'asc')->get();
    }

    public function crear(array $datos)
    {
        return Actividad::create($datos);
    }

    public function actualizar(Actividad $actividad, array $datos)
    {
        $actividad->update($datos);
        return $actividad;
    }

    public function eliminar(Actividad $actividad)
    {
        try {
            $actividad->delete();
            return true;
        } catch (Exception $e) {
            
            throw new Exception('No se puede eliminar: Esta actividad ya tiene grupos asignados.');
        }
    }
}