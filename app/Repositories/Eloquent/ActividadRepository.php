<?php
namespace App\Repositories\Eloquent;
use App\Models\Actividad;
use App\Repositories\Contracts\ActividadRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;



 class ActividadRepository implements ActividadRepositoryInterface
 {
    public function obtenerTodos(): Collection
    {
        return Actividad::orderby('nombre','asc')->get();
    }
    public function obtenerTodosById(int $id):?Actividad
    {
    return Actividad::find($id);
    }
    public function crear(array $data): Actividad
    {
        return Actividad::create($data);
    }
    public function actualizar(Actividad $actividad, array $datos): Actividad
    {
        $actividad->update($datos);
        return $actividad;
    }
    public function eliminar(Actividad $actividad): bool
    {
           return  $actividad->delete();
            
        
    }
 }