<?php

namespace App\Services\Actividad;

use App\Models\Actividad;
use App\Repositories\Contracts\ActividadRepositoryInterface;
use Exception;

class ActividadService
{
    protected $actividadRepository;
    public function __construct(ActividadRepositoryInterface $actividadRepository)
    {
        $this->actividadRepository = $actividadRepository;
    }
    public function obtenerTodos()
    {
        return $this->actividadRepository->obtenerTodos();
        
    }
    public function crear(array $datos)
    {
        return $this->actividadRepository->crear($datos);
    }

    public function actualizar(Actividad $actividad, array $datos)
    {
       return $this->actividadRepository->actualizar($actividad, $datos);
    }

    public function eliminar(Actividad $actividad)
    {
        try{
            $this->actividadRepository->eliminar($actividad);
        }catch(Exception $e){
            throw new Exception('No se puede eliminar: Esta actividad ya tiene grupos asignados.');
    }
    }
}