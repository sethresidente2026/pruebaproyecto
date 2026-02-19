<?php

namespace App\Exports;

use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\FromCollection;

class GruposExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Grupo::all();
    }
    public function headings(): array
    {
        return ['ID', 'Nombre del Grupo', 'Cupo Máximo', 'Actividad', 'Docente Asignado'];
    }
    public function map($grupo): array
    {
        return [
            $grupo->id,
            $grupo->nombre,
            $grupo->cupo_maximo,
            $grupo->actividad->nombre ?? 'N/A'
            ($grupo->docente->nombre ??'').' '.($grupo->docente->apellidos),
        ];

    }
}
