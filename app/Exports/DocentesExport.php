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
    /**
    * 1. OBTENER LOS DATOS
    */
    public function collection()
    {
        return Docente::all();
    }

    /**
    * 2. MAPEAR LOS DATOS
    */
    public function map($docente): array
    {
        return [
            $docente->id,
            $docente->nombre . ' ' . $docente->apellidos,
            $docente->email,
            $docente->estatus,
        ];
    }

    /**
    * 3. CABECERAS DE LA TABLA
    */
    public function headings(): array
    {
        return [
            'ID',
            'NOMBRE COMPLETO',
            'CORREO ELECTRÓNICO',
            'ESTATUS',
        ];
    }

    /**
    * 4. ANCHOS MANUALES DE COLUMNAS
    */
    public function columnWidths(): array
    {
        return [
            'A' => 25, // Más ancha para que el logo quepa perfecto
            'B' => 45, // 🔴 Forzamos la columna B a ser mucho más grande
        ];
    }

    /**
    * 5. EMPUJAR LA TABLA HACIA ABAJO (Bajamos a la fila 7 para dar respiro)
    */
    public function startCell(): string
    {
        return 'A7'; 
    }

    /**
    * 6. INSERTAR EL LOGO DE LA UGM
    */
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo UGM');
        $drawing->setDescription('Logo Oficial');
        $drawing->setPath(public_path('img/logo-ugm.png')); 
        $drawing->setHeight(75); 
        $drawing->setCoordinates('A1'); 
        
        // Separamos un poco el logo de las orillas para que no se vea amontonado
        $drawing->setOffsetX(15); 
        $drawing->setOffsetY(10);

        return $drawing;
    }

    /**
    * 7. MAGIA DE ESTILOS Y FUSIÓN DE CELDAS
    */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- A. FUSIÓN PARA EL LOGO ---
                $sheet->mergeCells('A1:A5');
            
                // --- B. FUSIÓN PARA EL TÍTULO (B1 hasta D5) ---
                $sheet->mergeCells('B1:D5'); 
                
                // Usamos \n para separar el título del subtítulo en la misma celda
                $sheet->setCellValue('B1', "REPORTE OFICIAL DE DOCENTES\nSISTEMA DE GESTIÓN ACADÉMICA - RECTORÍA CENTRO");

                // Formato del Título
                $sheet->getStyle('B1:D5')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER, // 🔴 Centrado vertical perfecto
                        'wrapText' => true, // 🔴 Obligatorio para que el \n funcione
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 16, // Letra más grande
                        'color' => ['argb' => 'FFD1101A'], // Rojo UGM
                    ],
                ]);

                // --- C. FORMATO DE LAS CABECERAS DE LA TABLA (Ahora en la Fila 7) ---
                $cabeceras = 'A7:D7'; 
                $sheet->getStyle($cabeceras)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1E293B'], // Azul oscuro elegante
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // --- D. APLICAR BORDES A TODA LA TABLA ---
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

                // Centrar la columna de ID y Estatus
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