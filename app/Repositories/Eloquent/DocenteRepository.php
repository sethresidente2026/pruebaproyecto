<?php
namespace App\Repositories\Eloquent;
use App\Models\Docente;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocenteRepository implements DocenteRepositoryInterface
{
  public function obtenerTodos(): Collection
  {
    return Docente::all();
  }


 public function obtenerPorId(string $id): ?Docente
 {
  return Docente::find($id);
 }
 public function crear(array $datos): Docente
 {
    return Docente::create($datos);
 }
 public function actualizar(string $id,array $datos) : Docente
 {
   $docenteAct=Docente::findOrFail($id);
   $docenteAct->update($datos);
   return $docenteAct;
 }
   public function eliminar(string $id): bool
 {
    $docente= Docente::findOrFail($id);
    return $docente->delete();
 }



}