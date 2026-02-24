<?php

namespace App\Services;

use App\Models\Asistencia;
use App\Exports\ReportePagosExport; // Suponiendo que así se llama tu export
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AsistenciaService
{
    public function obtenerTodos()
    {
        
        return Asistencia::with(['grupo', 'docente_titular', 'docente_sustituto'])
                         ->orderBy('fecha', 'desc')
                         ->get();
    }

    public function crear(array $datos)
    {
        
        if ($datos['estado'] !== 'Sustitución') {
            $datos['docente_sustituto_id'] = null;
        }

        return Asistencia::create($datos);
    }

    public function eliminar(Asistencia $asistencia)
    {
        $asistencia->delete();
        return true;
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