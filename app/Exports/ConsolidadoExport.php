<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class ConsolidadoExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function sheets(): array
    {
        return[
          'Docentes'=> new DocentesExport(),
          'Grupos'=> new GruposExport(),
          'Horarios'=> new HorariosExport(),
        ];
    }
}
