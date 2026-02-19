<?php

namespace App\Exports;

use App\Models\Horario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class HorariosExport implements FromCollection,WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
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
        return [
            ['AGENDA OFICIAL DE HORARIOS - UGM RECTORÍA CENTRO'],
            [''],
            ['Día', 'Hora Inicio', 'Hora Fin', 'Grupo (Actividad)', 'Docente', 'Espacio/Salón']
        ];
    }
    public function title(): string
    {
        return 'Agenda de Horarios';
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

            public function styles(Worksheet $sheet)
    {
        // Combinamos de la A a la F para el título principal
        $sheet->mergeCells('A1:F1');
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
