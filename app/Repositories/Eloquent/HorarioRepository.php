<?php 
namespace App\Repositories\Eloquent;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Horario;
use App\Repositories\Contracts\HorarioRepositoryInterface;

class HorarioRepository implements HorarioRepositoryInterface
{
    public function obtenerTodos(): Collection
    {
        return Horario::with(['grupo.actividad','grupo.docente','espacio'])->get();

    }
    public function crear(array $datos): Horario
    {
    return Horario::create($datos);
    }
    public function eliminar(Horario $horario): bool
    {
       return $horario->delete();
    }
    public function buscarEmpalmeEspacio(array $datos): ?Horario
    {
        return Horario::where('dia_semana', $datos['dia_semana'])
            ->where('espacio_id', $datos['espacio_id'])
            ->where(function ($query) use ($datos) {
                $query->whereBetween('hora_inicio', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhereBetween('hora_fin', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhere(function ($q) use ($datos) {
                          $q->where('hora_inicio', '<=', $datos['hora_inicio'])
                            ->where('hora_fin', '>=', $datos['hora_fin']);
                      });
            })->first();
    }
    public function buscarEmpalmesDocente(array $datos, string $docenteId): ?Horario
    {
        return Horario::where('dia_semana', $datos['dia_semana'])
            ->whereHas('grupo', fn($q) => $q->where('docente_id', $docenteId))
            ->where(function ($query) use ($datos) {
                $query->whereBetween('hora_inicio', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhereBetween('hora_fin', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhere(function ($q) use ($datos) {
                          $q->where('hora_inicio', '<=', $datos['hora_inicio'])
                            ->where('hora_fin', '>=', $datos['hora_fin']);
                      });
            })->first();
    }
    
}