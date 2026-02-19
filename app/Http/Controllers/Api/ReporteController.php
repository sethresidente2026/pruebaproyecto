<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\ConsolidadoExport;
use App\Exports\DocentesExport;
use App\Exports\GruposExport;
use App\Exports\HorariosExport;
class ReporteController extends Controller
{
    
    private function obtenerFecha() {
        return Carbon::now()->format('d_m_Y');
    }

    public function exportarDocentes() {
        return Excel::download(new DocentesExport, "Reporte_Docentes_{$this->obtenerFecha()}.xlsx");
    }

    public function exportarGrupos() {
        return Excel::download(new GruposExport, "Reporte_Grupos_{$this->obtenerFecha()}.xlsx");
    }

    public function exportarHorarios() {
        return Excel::download(new HorariosExport, "Reporte_Horarios_{$this->obtenerFecha()}.xlsx");
    }

    public function reporteGeneral() {
        return Excel::download(new ConsolidadoExport, "Reporte_Integral_UGM_{$this->obtenerFecha()}.xlsx");
    }
}
