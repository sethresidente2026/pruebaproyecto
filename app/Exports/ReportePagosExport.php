<?php

namespace App\Exports;

use App\Models\Docente;
use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
class ReportePagosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles,WithTitle
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
    public function styles(Worksheet $sheet)
    {
        
        $sheet->getStyle('A1:E' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ]);

        return [
            // Fila 1 (Encabezados): Fondo Rojo UGM, Letra Blanca, Negrita, Centrado
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC0392B'] // Código Hexadecimal del Rojo
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Centrar los datos numéricos de las columnas A, C, D y E
            'A' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'C' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
        ];
    }
    public function title(): string
    {
        return 'Cálculo de Pagos';
    }
}
