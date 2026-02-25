<?php
namespace App\Repositories\Eloquent;
use App\Models\Asistencia;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AsistenciaRepository implements AsistenciaRepositoryInterface
{
    public function obtenerTodasConRelaciones(): Collection
    {
        return Asistencia::with(['grupo','docente_titular','docente_sustituto'])
        ->orderBy('fecha','desc')
        ->get();
    }
        public function crear(array $datos): Asistencia
         {
        
        return Asistencia::create($datos);
        }

       
    
    public function eliminar(Asistencia $asistencia): bool
    {
        $asistencia->delete();
        return true;
    }
}