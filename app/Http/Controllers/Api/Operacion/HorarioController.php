<?php

namespace App\Http\Controllers\Api\Operacion;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Http\Requests\Horario\StoreHorarioRequest;
use App\Services\Horario\HorarioService;
use Exception;

class HorarioController extends Controller
{
    protected $horarioService;

    public function __construct(HorarioService $horarioService)
    {
        $this->horarioService = $horarioService;
    }

    public function index()
    {
        return response()->json($this->horarioService->obtenerTodos(), 200);
    }

    public function store(StoreHorarioRequest $request)
    {
        try {
            
            $horario = $this->horarioService->crear($request->validated());
            
            return response()->json([
                'mensaje' => 'Horario asignado correctamente',
                'data' => $horario
            ], 201);

        } catch (Exception $e) {
            
            return response()->json(['mensaje' => $e->getMessage()], 409);
        }
    }

  
    public function destroy(Horario $horario)
    {
        $this->horarioService->eliminar($horario);
        return response()->json(['mensaje' => 'Horario liberado correctamente'], 200);
    }

    public function exportarExcel()
    {
        return $this->horarioService->exportarExcel();
    }
}