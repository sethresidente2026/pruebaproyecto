<?php
namespace App\Services\Docente;

use App\Exports\DocentesExport;
use App\Models\Docente;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Repositories\Contracts\DocenteRepositoryInterface;


class DocenteService 
{
    protected $docenteRepository;
    public function __construct(DocenteRepositoryInterface $docenteRepository)
    {
        $this->docenteRepository = $docenteRepository;
    }
public function obtenerTodos()
{
return $this->docenteRepository->obtenerTodos();;
}
public function crear(array $datos)
{
    return $this->docenteRepository->crear($datos);
}
public function obtenerPorId(string $id)
{
return $this->docenteRepository->obtenerPorId($id);
}
public function eliminar(string $id)
{
   return $this->docenteRepository->eliminar($id);
}
public function actualizar(string $id,array $datos)
{
     return $this->docenteRepository->actualizar($id, $datos);
}
public function exportarExcel()
{
$fecha=Carbon::now()->format('d_m_y');
$nombreArchivo ="Reporte_Docentes_UGM_{$fecha}.xlsx";
return Excel::download(new DocentesExport, $nombreArchivo);


}

}