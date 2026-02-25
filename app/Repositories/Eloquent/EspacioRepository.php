<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\EspacioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Espacio;

class EspacioRepository implements EspacioRepositoryInterface
{
    public function obtenerTodos(): Collection
    {
        return Espacio::all();
    }
    public function encontrarPorId(string $id): ?Espacio
    {
        return Espacio::find($id);
    }
    public function crear(array $data): Espacio
    {
        return Espacio::create($data);
    }
    public function actualizar(Espacio $espacio, array $datos): Espacio
    {
        $espacio->update($datos);
        return $espacio;
    }
    public function eliminar(Espacio $espacio): bool
    {
        return $espacio->delete();
    }
}