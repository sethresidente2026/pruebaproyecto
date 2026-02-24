<?php

namespace App\Services;

use App\Models\Grupo;
use App\Exports\GruposExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class GrupoService
{
    public function obtenerTodos()
{
    return Grupo::with(['actividad', 'docente', 'ciclo', 'nivelEducativo'])->get();
}

    public function crear(array $datos)
    {
        return Grupo::create($datos);
    }

    public function actualizar(Grupo $grupo, array $datos)
    {
       
        $grupo->update($datos);
        
        
        return $grupo->load(['actividad', 'docente', 'ciclo', 'nivelEducativo']);
    }

    public function eliminar(Grupo $grupo)
    {
        $grupo->delete();
        return true;
    }

    public function exportarExcel()
    {
        $fecha = Carbon::now()->format('d_m_Y');
        return Excel::download(new GruposExport, "Reporte_Grupos_UGM_{$fecha}.xlsx");
    }
}