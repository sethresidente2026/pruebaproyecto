<?php
namespace App\Services\Espacio;
use App\Models\Espacio;
use App\Repositories\Contracts\EspacioRepositoryInterface;
class EspacioService
{
    private $espacioRepository;
    public function __construct(EspacioRepositoryInterface $espacioRepository)
    {
        $this->espacioRepository= $espacioRepository;
    }
     public function obtenerTodos()
    {
      return $this->espacioRepository->obtenerTodos();
    }
    public function crear(array $datos)
    {
        return $this->espacioRepository->crear($datos);
    }
    public function actualizar(Espacio $espacio,array $datos)
    {
        return $this->espacioRepository->actualizar($espacio, $datos);
    }
    public function eliminar(Espacio $espacio)
    {
         return $this->espacioRepository->eliminar($espacio);
    }
}