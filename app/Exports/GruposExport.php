<?php

namespace App\Exports;

use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class GruposExport implements FromCollection,WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles

{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Grupo::with(['actividad', 'docente', 'ciclo', 'nivel'])->get();
    }
    public function title(): string
    {
        return 'Catalgo de Grupos';
    }
    public function headings(): array
    {
        return [
         ['REPORTE OFICIAL DE GRUPOS - UGM RECTORÍA CENTRO'],
            [''],
            ['ID', 'Nombre del Grupo', 'Cupo Máximo', 'Actividad', 'Docente Asignado', 'Ciclo Escolar', 'Nivel']
    ];
    }
    public function map($grupo): array
    {
        return [
            $grupo->id,
            $grupo->nombre,
            $grupo->cupo_maximo,
            $grupo->actividad->nombre ?? 'N/A',
            ($grupo->docente->nombre ?? '') . ' ' . ($grupo->docente->apellidos ?? 'Sin asignar'),
            $grupo->ciclo->nombre ?? 'N/A',
            $grupo->nivel->nombre ?? 'N/A',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Combinamos de la A a la G para el título principal
        $sheet->mergeCells('A1:G1');
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
