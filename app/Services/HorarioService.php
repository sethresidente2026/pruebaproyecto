<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\Grupo;
use App\Exports\HorariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;

class HorarioService
{
    public function obtenerTodos()
    {
        // Traemos los horarios con sus relaciones listas para Vue
        return Horario::with(['grupo.actividad', 'grupo.docente', 'espacio'])->get();
    }

    public function crear(array $datos)
    {
        // 1. Ejecutamos la regla de negocio crítica
        $this->verificarEmpalmes($datos);

        // 2. Si pasa la prueba sin lanzar error, lo creamos
        return Horario::create($datos);
    }

    public function eliminar(Horario $horario)
    {
        $horario->delete();
        return true;
    }

    public function exportarExcel()
    {
        $fecha = Carbon::now()->format('d_m_Y');
        return Excel::download(new HorariosExport, "Reporte_Horarios_UGM_{$fecha}.xlsx");
    }

    /**
     * LÓGICA DE NEGOCIO: Evitar choques de horarios
     */
    private function verificarEmpalmes(array $datos)
    {
        $grupo = Grupo::findOrFail($datos['grupo_id']);
        $docente_id = $grupo->docente_id;

        // Regla 1: ¿El ESPACIO está ocupado a esa hora?
        $empalmeEspacio = Horario::where('dia_semana', $datos['dia_semana'])
            ->where('espacio_id', $datos['espacio_id'])
            ->where(function ($query) use ($datos) {
                $query->whereBetween('hora_inicio', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhereBetween('hora_fin', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhere(function ($q) use ($datos) {
                          $q->where('hora_inicio', '<=', $datos['hora_inicio'])
                            ->where('hora_fin', '>=', $datos['hora_fin']);
                      });
            })->first();

        if ($empalmeEspacio) {
            throw new Exception("Conflicto: El espacio ya está ocupado por el grupo '{$empalmeEspacio->grupo->nombre}'.");
        }

        // Regla 2: ¿El DOCENTE ya tiene otra clase a esa hora?
        $empalmeDocente = Horario::where('dia_semana', $datos['dia_semana'])
            ->whereHas('grupo', function($q) use ($docente_id) {
                $q->where('docente_id', $docente_id);
            })
            ->where(function ($query) use ($datos) {
                $query->whereBetween('hora_inicio', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhereBetween('hora_fin', [$datos['hora_inicio'], $datos['hora_fin']])
                      ->orWhere(function ($q) use ($datos) {
                          $q->where('hora_inicio', '<=', $datos['hora_inicio'])
                            ->where('hora_fin', '>=', $datos['hora_fin']);
                      });
            })->first();

        if ($empalmeDocente) {
            throw new Exception("Conflicto: El docente asignado a este grupo ya imparte otra clase en este horario.");
        }
    }
}