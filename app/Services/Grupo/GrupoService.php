<?php

namespace App\Services\Grupo;

use App\Models\Grupo;
use App\Exports\GruposExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Repositories\Contracts\GrupoRepositoryInterface;
class GrupoService
{
    protected $grupoRepository;
    public function __construct(GrupoRepositoryInterface $grupoRepository)
    {
        $this->grupoRepository = $grupoRepository;
    }
    public function obtenerTodos()
{
    return $this->grupoRepository->obtenerTodos();
}

    public function crear(array $datos)
    {
        return $this->grupoRepository->crear($datos);
    }

    public function actualizar(Grupo $grupo, array $datos)
    {
       return $this->grupoRepository->actualizar($grupo, $datos);
        
    }

    public function eliminar(Grupo $grupo)
    {
        return $this->grupoRepository->eliminar($grupo);
    }

    public function exportarExcel()
    {
        $fecha = Carbon::now()->format('d_m_Y');
        return Excel::download(new GruposExport, "Reporte_Grupos_UGM_{$fecha}.xlsx");
    }
}