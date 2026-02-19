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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;

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

    public function generarPDF(){
            $docentes=Docente::all();
            $grupos = Grupo::with(['docente', 'actividad', 'ciclo', 'nivel'])->get();
            $horarios = Horario::with(['grupo.docente', 'espacio'])->orderBy('dia_semana')->orderBy('hora_inicio')->get();
            $fecha = Carbon::now()->format('d/m/Y');

        // 2. Cargamos la vista y le pasamos las variables
             $pdf = Pdf::loadView('pdf.reporte_integral', compact('docentes', 'grupos', 'horarios', 'fecha'));
        
        // 3. Configuramos el papel (A4, formato vertical)
                $pdf->setPaper('A4', 'portrait');

        // 4. Forzamos la descarga con un nombre corporativo
                $nombreArchivo = "Reporte_Operacion_UGM_" . Carbon::now()->format('d_m_Y') . ".pdf";
                return $pdf->download($nombreArchivo);
    }
}
