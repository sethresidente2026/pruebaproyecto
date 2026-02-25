<?php
namespace App\Repositories\Contracts;

use App\Models\Asistencia;
use Illuminate\Database\Eloquent\Collection;
interface AsistenciaRepositoryInterface
{
    public function obtenerTodasConRelaciones() : Collection;
    public function crear(array $datos) : Asistencia; 
    public function eliminar(Asistencia $asistencia): bool;
    }