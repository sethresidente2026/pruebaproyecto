<?php

namespace App\Exports;
use App\Models\Horario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class HorariosExport implements 
FromCollection,
WithHeadings,
WithMapping,
WithDrawings,
WithEvents,
WithColumnWidths,
WithTitle,
ShouldAutoSize,
WithCustomStartCell

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
            'Día', 
            'Hora Inicio', 
            'Hora Fin', 
            'Grupo (Actividad)', 
            'Docente', 
            'Espacio/Salón'
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
            $drawing=new Drawing();
            $drawing->setName('Logo UGM');
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

                // Fusión para el logo
                $sheet->mergeCells('A1:A5');

                
                $sheet->mergeCells('B1:F5'); 
                
                $sheet->setCellValue('B1', "REPORTE OFICIAL DE GRUPOS\nSISTEMA DE GESTIÓN ACADÉMICA - RECTORÍA CENTRO");

                // Formato del Título
                $sheet->getStyle('B1:F5')->applyFromArray([
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

                
                $cabeceras = 'A7:F7'; 
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
                $rangoTabla = 'A7:F7' . $ultimaFila; 

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
                $sheet->getStyle('F8:H' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ]; 
        }
            
            
}
