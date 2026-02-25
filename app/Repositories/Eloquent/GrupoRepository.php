<?php
namespace App\Repositories\Eloquent;
use App\Models\Grupo;
use App\Repositories\Contracts\GrupoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
class GrupoRepository implements GrupoRepositoryInterface
{
    public function obtenerTodos(): Collection
    {
        return Grupo::with(['actividad','docente','ciclo','nivelEducativo'])->get();
    }
    public function crear(array $datos): Grupo
    {
        return Grupo::create($datos);
    }
    public function actualizar(Grupo $grupo, array $datos): Grupo
    {
        $grupo->update($datos);
        return $grupo->load(['actividad','docente','ciclo','nivelEducativo']);
    }
    public function eliminar(Grupo $grupo): bool
    {
        return $grupo->delete();
    }
}