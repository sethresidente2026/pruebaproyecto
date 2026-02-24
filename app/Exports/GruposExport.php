<?php

namespace App\Exports;

use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GruposExport implements 
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithCustomStartCell,
    WithDrawings,
    WithEvents,
    WithColumnWidths,
    WithTitle
{
    public function collection()
    {
        return Grupo::with(['actividad', 'docente', 'ciclo', 'nivelEducativo'])->get();
    }

    public function title(): string
    {
        return 'Catálogo de Grupos';
    }

    public function headings(): array
    {
        return [
            'ID', 
            'NOMBRE DEL GRUPO', 
            'CUPO MÁXIMO', 
            'ACTIVIDAD', 
            'DOCENTE ASIGNADO', 
            'CICLO ESCOLAR', 
            'NIVEL',
            '¿ES MIXTO?' 
        ];
    }

   // app/Exports/GruposExport.php

public function map($grupo): array
{
    return [
        $grupo->id,
        $grupo->nombre,
        $grupo->cupo_maximo,
        $grupo->actividad->nombre ?? 'N/A',
        ($grupo->docente->nombre ?? '') . ' ' . ($grupo->docente->apellidos ?? ''),
        $grupo->ciclo->nombre ?? 'N/A',
        
        $grupo->nivel_educativo->nombre ?? 'N/A', 
       
        $grupo->nivel == 'Mixto' ? 'SÍ' : 'NO', 
    ];
}

    public function columnWidths(): array
    {
        return [
            'A' => 25, 
            'B' => 35, 
        ];
    }

    public function startCell(): string
    {
        return 'A7'; 
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo UGM');
        $drawing->setDescription('Logo Oficial');
        $drawing->setPath(public_path('img/logo-ugm.png')); 
        $drawing->setHeight(75); 
        $drawing->setCoordinates('A1'); 
        $drawing->setOffsetX(25); 
        $drawing->setOffsetY(10);

        return $drawing;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Fusión para el logo
                $sheet->mergeCells('A1:A5');

                
                $sheet->mergeCells('B1:H5'); 
                
                $sheet->setCellValue('B1', "REPORTE OFICIAL DE GRUPOS\nSISTEMA DE GESTIÓN ACADÉMICA - RECTORÍA CENTRO");

                // Formato del Título
                $sheet->getStyle('B1:H5')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true, 
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 16, 
                        'color' => ['argb' => 'FFD1101A'], 
                    ],
                ]);

                
                $cabeceras = 'A7:H7'; 
                $sheet->getStyle($cabeceras)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1E293B'], 
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $ultimaFila = $sheet->getHighestRow();
                $rangoTabla = 'A7:H7' . $ultimaFila; 

                $sheet->getStyle($rangoTabla)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFBDC3C7'], 
                        ],
                    ],
                ]);

                // Centrar columnas específicas (ID, Cupo Máximo y Mixto)
                $sheet->getStyle('A8:A' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C8:C' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H8:H' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}