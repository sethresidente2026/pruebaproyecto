<?php

namespace App\Repositories\Contracts;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Collection;
interface GrupoRepositoryInterface
{
public function obtenerTodos(): Collection;
public function crear(array $datos): Grupo;
public function actualizar(Grupo $grupo, array $datos): Grupo;
public function eliminar(Grupo $grupo): bool;
}
