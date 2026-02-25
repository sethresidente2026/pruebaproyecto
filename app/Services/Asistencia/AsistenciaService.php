<?php

namespace App\Services\Asistencia;

use App\Models\Asistencia;
use App\Exports\ReportePagosExport; // Suponiendo que así se llama tu export
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Repositories\Contracts\AsistenciaRepositoryInterface;
class AsistenciaService
{
    protected $asistenciaRepository;
    public function __construct(AsistenciaRepositoryInterface $asistenciaRepository)
    {
        $this->asistenciaRepository = $asistenciaRepository;
    }
    public function obtenerTodos()
    {
        return $this->asistenciaRepository->obtenerTodasConRelaciones();
    }

    public function crear(array $datos)
    {
        
        if (isset($datos['estado']) && $datos['estado'] !== 'Sustitución') {
            $datos['docente_sustituto_id'] = null;
        }

        return $this->asistenciaRepository->crear($datos);
    }

    public function eliminar(Asistencia $asistencia)
    {
        return $this->asistenciaRepository->eliminar($asistencia);
    }

    public function exportarReportePagos()
    {
        // Generamos el nombre dinámico con el mes y año actual
        $mesActual = Carbon::now()->locale('es')->monthName;
        $anio = Carbon::now()->year;
        
        $nombreArchivo = "Reporte_Pagos_UGM_{$mesActual}_{$anio}.xlsx";

        return Excel::download(new ReportePagosExport, $nombreArchivo);
    }
    
}