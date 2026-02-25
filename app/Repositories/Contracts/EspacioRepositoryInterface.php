<?php
namespace App\Repositories\Contracts;
use App\Models\Espacio;
use Illuminate\Database\Eloquent\Collection;
interface EspacioRepositoryInterface
{
public function obtenerTodos(): Collection;
public function encontrarPorId(string $id): ?Espacio;
public function crear(array $datos): Espacio;
public function actualizar(Espacio $espacio, array $datos): Espacio;
public function eliminar(Espacio $espacio): bool;


}