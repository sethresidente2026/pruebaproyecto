<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Exports\ReportePagosExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
class AsistenciaController extends Controller
{
public function index()
{
    $asistencia=Asistencia::with(['grupo', 'docenteTitular', 'docenteSustituto'])
    ->orderBy('fecha','desc')
    ->get();
    return response()->json($asistencia,200);
}
public function store(Request $request)
{
    $request->validate([
     'grupo_id' => 'required|exists:grupos,id',
            'docente_id' => 'required|exists:docentes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:Asistió,Falta,Retardo,Sustitución',
            'docente_sustituto_id' => 'nullable|exists:docentes,id',
            'observaciones' => 'nullable|string'
    ]);
    $asistencia = Asistencia::create($request->all());
    return response()->json([
    'mensaje'=>'Asistencia registrada correctamentes',    
    'asistencia'=> $asistencia,
    ],200);
}
public function destroy(string $id)
    {
        $asistencia = Asistencia::find($id);
        if(!$asistencia) return response()->json(['mensaje' => 'Registro no encontrado'], 404);
        
        $asistencia->delete();
        return response()->json(['mensaje' => 'Registro eliminado'], 200);
    }

    
    public function exportarPagos()
    {
        $fecha = Carbon::now()->format('d_m_Y');
        return Excel::download(new ReportePagosExport, "Reporte_Docentes_UGM_{$fecha}.xlsx");
    }
}
