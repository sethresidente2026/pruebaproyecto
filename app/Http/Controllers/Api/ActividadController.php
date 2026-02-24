<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Http\Requests\StoreActividadRequest;
use App\Http\Requests\UpdateActividadRequest;
use App\Services\ActividadService;
use Exception;

class ActividadController extends Controller
{
    protected $actividadService;

    public function __construct(ActividadService $actividadService)
    {
        $this->actividadService = $actividadService;
    }

    public function index()
    {
        return response()->json($this->actividadService->obtenerTodos(), 200);
    }

    public function store(StoreActividadRequest $request)
    {
        $actividad = $this->actividadService->crear($request->validated());

        return response()->json([
            'mensaje' => 'Actividad creada con éxito',
            'data'    => $actividad
        ], 201);
    }

    public function show(Actividad $actividad)
    {
        return response()->json($actividad, 200);
    }

    public function update(UpdateActividadRequest $request, Actividad $actividad)
    {
        $actividadActualizada = $this->actividadService->actualizar($actividad, $request->validated());

        return response()->json([
            'mensaje' => 'Actividad actualizada correctamente',
            'data'    => $actividadActualizada
        ], 200);
    }

    public function destroy(Actividad $actividad)
    {
        try {
            $this->actividadService->eliminar($actividad);
            return response()->json(['mensaje' => 'Actividad eliminada correctamente'], 200);
            
        } catch (Exception $e) {
            // 🔴 Atrapamos el error de la llave foránea y mandamos código 422
            return response()->json(['mensaje' => $e->getMessage()], 422);
        }
    }
}