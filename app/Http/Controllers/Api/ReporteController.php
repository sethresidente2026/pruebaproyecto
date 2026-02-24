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
use Illuminate\Support\Facades\File;
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
        // 1. Obtenemos todos los datos optimizados con Eager Loading (para que el PDF sea rápido)
        $docentes = Docente::all();
        
        // 🔴 Usamos 'nivelEducativo' como lo corregimos antes
        $grupos = Grupo::with(['actividad', 'docente', 'ciclo', 'nivelEducativo'])->get();
        
        // Traemos los horarios con la info del grupo y del espacio
        $horarios = Horario::with(['grupo.actividad', 'grupo.docente', 'espacio'])->get();

        // 2. Convertimos el Logo de la UGM a Base64
        // Asegúrate de que la imagen exista en public/img/logo-ugm.png
        $rutaLogo = public_path('img/logo-ugm.png');
        $logoBase64 = '';
        
        if (File::exists($rutaLogo)) {
            $logoData = base64_encode(file_get_contents($rutaLogo));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        // 3. Cargamos la vista de Blade y le pasamos todas las variables
        $pdf = Pdf::loadView('pdf.reporte_integral', [
            'logo'     => $logoBase64,
            'fecha'    => now()->format('d/m/Y H:i'),
            'docentes' => $docentes,
            'grupos'   => $grupos,
            'horarios' => $horarios,
        ]);

        // Opcional: Configurar el tamaño del papel y la orientación
        $pdf->setPaper('A4', 'portrait');

        // 4. Descargamos el archivo
        return $pdf->download('Reporte_Integral_UGM.pdf');
    }
}
