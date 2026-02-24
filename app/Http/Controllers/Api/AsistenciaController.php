<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Http\Requests\StoreAsistenciaRequest;
use App\Services\AsistenciaService;

class AsistenciaController extends Controller
{
    protected $asistenciaService;

    public function __construct(AsistenciaService $asistenciaService)
    {
        $this->asistenciaService = $asistenciaService;
    }

    public function index()
    {
        return response()->json($this->asistenciaService->obtenerTodos(), 200);
    }

    public function store(StoreAsistenciaRequest $request)
    {
        
        $asistencia = $this->asistenciaService->crear($request->validated());

        return response()->json([
            'mensaje' => 'Asistencia registrada correctamente',
            'data'    => $asistencia
        ], 201);
    }

    
    public function destroy(Asistencia $asistencia)
    {
        $this->asistenciaService->eliminar($asistencia);
        
        return response()->json(['mensaje' => 'Registro eliminado'], 200);
    }

    public function exportarPagos()
    {
        return $this->asistenciaService->exportarReportePagos();
    }
}