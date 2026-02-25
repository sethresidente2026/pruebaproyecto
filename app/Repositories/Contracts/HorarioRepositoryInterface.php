<?php 
namespace App\Repositories\Contracts;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Collection;

interface HorarioRepositoryInterface
{
    public function obtenerTodos(): Collection;
    public function crear(array $datos): Horario;
    public function eliminar(Horario $horario): bool;
    public function buscarEmpalmeEspacio(array $datos): ?Horario;
    public function buscarEmpalmesDocente(array $datos,string $docenteId):?Horario;
}