<?php

namespace App\Http\Controllers\Api\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Http\Requests\Actividad\UpdateActividadRequest;
use App\Http\Requests\Actividad\StoreActividadRequest;
use App\Services\Actividad\ActividadService;
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
            
            return response()->json(['mensaje' => $e->getMessage()], 422);
        }
    }
}