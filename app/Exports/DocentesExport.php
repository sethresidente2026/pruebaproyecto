<?php

namespace App\Exports;

use App\Models\Docente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocentesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithTitle,WithStyles
{
    public function collection()
    {
        // Traemos a todos sin excepción para el reporte administrativo
        return Docente::all();
    }
public function title(): string
{
    return 'Plantilla docente';
}
    public function headings(): array
    {
        return [
            ['REPORTE OFICIAL DE DOCENTES - UGM RECTORÍA CENTRO'],
            [''],
            ['ID','Nombre del Docente ','Correo Electronico','Estatus','Fecha de Registro'],
        ];
    }

    public function map($docente): array
    {
        return [
            $docente->id,
            // Juntamos Nombre, Apellidos y Fecha en una sola celda profesional
            $docente->nombre . ' ' . $docente->apellidos . ' [' . Carbon::now()->format('d/m/Y') . ']',
            $docente->email,
            strtoupper($docente->estatus), // Lo ponemos en mayúsculas para que resalte
            Carbon::parse($docente->created_at)->format('d/m/Y H:i'), // Cuándo se dio de alta
        ];
    }
    public function styles(Worksheet $sheet)
    {
   
        $sheet->mergeCells('A1:E1');
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']],
            3 => ['font' => ['bold' => true]],
        ];
    }
}