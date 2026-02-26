<?php

namespace App\Services\Horario;

use App\Models\Horario;
use App\Models\Grupo;
use App\Exports\HorariosExport;
use App\Repositories\Contracts\HorarioRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

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
       $horariosCreados = [];

        
        DB::transaction(function () use ($datos, &$horariosCreados) {
            
            
            foreach ($datos['dias'] as $dia) {
                
               
                $datosDia = $datos;
                $datosDia['dia_semana'] = $dia; 
                
                
                $this->verificarEmpalmes($datosDia);
                
               
                $horariosCreados[] = $this->horarioRepository->crear($datosDia);
            }
        });
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

   
    private function verificarEmpalmes(array $datosDia)
    {
        $grupo = Grupo::findOrFail($datosDia['grupo_id']);
        
        
        $empalmevacio = $this->horarioRepository->buscarEmpalmeEspacio($datosDia);
        if($empalmevacio){
            
            throw new Exception("Conflicto: El espacio ya está ocupado el {$datosDia['dia_semana']} por el grupo '{$empalmevacio->grupo->nombre}'.");
        }

        $empalmeDocente = $this->horarioRepository->buscarEmpalmesDocente($datosDia, $grupo->docente_id);
        if($empalmeDocente){
            throw new Exception("Conflicto: El docente asignado ya tiene clase el {$datosDia['dia_semana']} en este horario.");
        }
    }
}