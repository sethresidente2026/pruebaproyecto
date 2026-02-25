<?php
namespace App\Repositories\Contracts;
use App\Models\Docente;
use Illuminate\Database\Eloquent\Collection;

interface DocenteRepositoryInterface
{
public function obtenerTodos(): Collection;
public function obtenerPorId(string $id): ?Docente;
public function crear(array $datos): Docente;
public  function actualizar(string $id,array $datos): Docente;
public function eliminar(string $id) : bool;

}