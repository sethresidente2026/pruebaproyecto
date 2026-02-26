<?php
namespace App\Services\Docente;

use App\Exports\DocentesExport;
use App\Models\Docente;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use Exception;

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
     // 1. Instanciamos al docente usando tu propia función
        $docente = $this->obtenerPorId($id);

        // 2. Verificamos si intentan desactivarlo
        if (isset($datos['estatus']) && in_array($datos['estatus'], ['Inactivo', 'Baja Temporal'])) {
            
            // Si el estatus que mandan es diferente al que ya tiene el docente...
            if ($docente->estatus !== $datos['estatus']) {
                $this->verificarGruposAsignados($docente, "No puedes dar de baja a este docente porque tiene grupos activos.");
            }
        }

        // 3. Si pasa la validación, mandamos el ID y los datos a tu repositorio
        return $this->docenteRepository->actualizar($id, $datos);
}
public function exportarExcel()
{
$fecha=Carbon::now()->format('d_m_y');
$nombreArchivo ="Reporte_Docentes_UGM_{$fecha}.xlsx";
return Excel::download(new DocentesExport, $nombreArchivo);


}
private function verificarGruposAsignados(Docente $docente, string $mensajeError)
    {
        if ($docente->grupos()->count() > 0) {
            throw new Exception($mensajeError);
        }
    }
    
}