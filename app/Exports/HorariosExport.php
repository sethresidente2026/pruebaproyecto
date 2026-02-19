<?php

namespace App\Exports;

use App\Models\Horario;
use Maatwebsite\Excel\Concerns\FromCollection;

class HorariosExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Horario::with(['grupo.docente', 'grupo.actividad', 'espacio'])->get();
    }
    public function headings(): array
    {
        return[
            'Dia',
            'Hora Inicio',
            'Hora Fin',
            'Grupo (Actividad)',
            'Docente',
            'Espacio/Salón',
        ];
    }
    public function map($horario): array
    {
        return[
        $horario->dia_semana,
            $horario->hora_inicio,
            $horario->hora_fin,
            ($horario->grupo->nombre ?? 'N/A') . ' (' . ($horario->grupo->actividad->nombre ?? '') . ')',
            ($horario->grupo->docente->nombre ?? '') . ' ' . ($horario->grupo->docente->apellidos ?? ''),
            $horario->espacio->nombre ?? 'Sin asignar'
        ];
            }
}
