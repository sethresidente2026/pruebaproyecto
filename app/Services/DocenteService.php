<?php
namespace App\Services;

use App\Exports\DocentesExport;
use App\Models\Docente;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class DocenteService 
{
public function obtenerTodos(){
    return Docente::all();
}
public function crear(array $datos)
{
    return Docente::create($datos);
}
public function obtenerPorId(string $id)
{
return Docente::findOrFail($id);
}
public function eliminar(string $id)
{
    $docente=Docente::findOrFail($id);
    $docente->delete();
    return true;
}
public function actualizar(string $id,array $datos)
{
    $docente=Docente::findOrFail($id);
    $docente->update($datos);
    return $docente;
}
public function exportarExcel()
{
$fecha=Carbon::now()->format('d_m_y');
$nombreArchivo ="Reporte_Docentes_UGM_{$fecha}.xlsx";
return Excel::download(new DocentesExport, $nombreArchivo);


}

}