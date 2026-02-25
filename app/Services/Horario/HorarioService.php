<?php

namespace App\Services\Horario;

use App\Models\Horario;
use App\Models\Grupo;
use App\Exports\HorariosExport;
use App\Repositories\Contracts\HorarioRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;

class HorarioService
{
    protected $horarioRepository;
    public function __construct(HorarioRepositoryInterface $horarioRepository){
        $this->horarioRepository = $horarioRepository;
    }
    public function obtenerTodos()
    {
     return $this->horarioRepository->obtenerTodos();
    }

    public function crear(array $datos)
    {
        $this->verificarEmpalmes($datos);
        return $this->horarioRepository-> crear($datos);

    }

    public function eliminar(Horario $horario)
    {
        $this->horarioRepository->eliminar($horario);
    }

    public function exportarExcel()
    {
        $fecha = Carbon::now()->format('d_m_Y');
        return Excel::download(new HorariosExport, "Reporte_Horarios_UGM_{$fecha}.xlsx");
    }

   
    private function verificarEmpalmes(array $datos)
    {
        $grupo = Grupo::findOrFail($datos['grupo_id']);
        $empalmevacio=$this->horarioRepository->buscarEmpalmeEspacio($datos);
        if($empalmevacio){
         throw new Exception("Conflicto: El espacio ya está ocupado por el grupo '{$empalmevacio->grupo->nombre}'.");
        }

        $empalmeDocente=$this->horarioRepository->buscarEmpalmesDocente($datos,$grupo->docente_id);
        if($empalmeDocente){
            throw new Exception("Conflicto: El docente asignado ya tiene otra clase en este horario.");
        }
    }
}