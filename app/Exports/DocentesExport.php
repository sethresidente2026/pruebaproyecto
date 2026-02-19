<?php

namespace App\Exports;

use App\Models\Docente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class DocentesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        // Traemos a todos sin excepción para el reporte administrativo
        return Docente::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre del Docente [Fecha de Reporte]',
            'Correo Electrónico',
            'Estatus en Sistema',
            'Fecha de Registro',
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
}