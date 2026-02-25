<?php
namespace App\Repositories\Contracts;
use App\Models\Actividad;
use Illuminate\Database\Eloquent\Collection;
interface ActividadRepositoryInterface
{
 public function obtenerTodos(): Collection;
 public function obtenerTodosById(int $id): ?Actividad;
 public function crear(array $datos): Actividad;
 public function actualizar(Actividad $actividad,array $datos): Actividad;
 public function eliminar(Actividad $actividad): bool;

}