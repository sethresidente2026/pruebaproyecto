<?php

namespace App\Exports;

use App\Models\Docente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths; 
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocentesExport implements 
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
        return Docente::all();
    }

   
    public function map($docente): array
    {
        return [
            $docente->id,
            $docente->nombre . ' ' . $docente->apellidos,
            $docente->email,
            $docente->estatus,
        ];
    }

    
    public function headings(): array
    {
        return [
            'ID',
            'NOMBRE COMPLETO',
            'CORREO ELECTRÓNICO',
            'ESTATUS',
        ];
    }

   
    public function columnWidths(): array
    {
        return [
            'A' => 25, 
            'B' => 45, 
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
        
        
        $drawing->setOffsetX(15); 
        $drawing->setOffsetY(10);

        return $drawing;
    }

   
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:A5');
                $sheet->mergeCells('B1:D5'); 
                
                
                $sheet->setCellValue('B1', "REPORTE OFICIAL DE DOCENTES\nSISTEMA DE GESTIÓN ACADÉMICA - RECTORÍA CENTRO");

               
                $sheet->getStyle('B1:D5')->applyFromArray([
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

               
                $cabeceras = 'A7:D7'; 
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
                $rangoTabla = 'A7:D' . $ultimaFila; 

                $sheet->getStyle($rangoTabla)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFBDC3C7'], 
                        ],
                    ],
                ]);

                
                $sheet->getStyle('A8:A' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D8:D' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
     public function title(): string
    {
        return 'Reporte de Docentes';
    }
}