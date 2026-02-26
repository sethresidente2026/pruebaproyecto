<?php

namespace App\Exports;
use App\Models\Docente;
use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class ReportePagosExport implements
FromCollection,
WithHeadings,
ShouldAutoSize,
WithCustomStartCell,
WithEvents,
WithTitle,

WithDrawings,
WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       $docentes=Docente::all();
       $reporte=collect();

       foreach ($docentes as $docente) {
            $clasesTitular = Asistencia::where('docente_id', $docente->id)
                                       ->whereIn('estado', ['Asistió', 'Retardo'])
                                       ->count();

            $clasesSustituto = Asistencia::where('docente_sustituto_id', $docente->id)
                                         ->where('estado', 'Sustitución')
                                         ->count();

            $totalClases = $clasesTitular + $clasesSustituto;

            if ($totalClases > 0) {
                $reporte->push([
                    'ID Docente' => $docente->id,
                    'Nombre Completo' => $docente->nombre . ' ' . $docente->apellidos,
                    'Clases como Titular' => $clasesTitular,
                    'Clases como Sustituto' => $clasesSustituto,
                    'TOTAL CLASES' => $totalClases
                ]);
            }
        }

        return $reporte;
    }
    public function headings(): array
    {
        return [
            'ID Docente',
            'Nombre Completo',
            'Clases como Titular',
            'Clases como Sustituto',
            'TOTAL CLASES'
        ];
    }
    
    public function title(): string
    {
        return 'Cálculo de Pagos';
    }

    public function columnWidths(): array
    {
        return[
        'A'=>25,
        'B'=>45
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
            
               
                $sheet->mergeCells('B1:E5'); 
                
                
                $sheet->setCellValue('B1', "REPORTE OFICIAL DE DOCENTES\nSISTEMA DE GESTIÓN ACADÉMICA - RECTORÍA CENTRO");

             
                $sheet->getStyle('B1:E5')->applyFromArray([
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

              
                $cabeceras = 'A7:E7'; 
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

                // Centrar la columna de ID y Estatus
                $sheet->getStyle('A8:A' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D8:D' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

}
