<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Services\GrupoService;

class GrupoController extends Controller
{
    protected $grupoService;

    public function __construct(GrupoService $grupoService)
    {
        $this->grupoService = $grupoService;
    }

    public function index()
    {
        return response()->json($this->grupoService->obtenerTodos(), 200);
    }

    public function store(StoreGrupoRequest $request)
    {
        // Pasamos únicamente la data validada
        $grupo = $this->grupoService->crear($request->validated());

        return response()->json([
            'mensaje' => 'Grupo registrado correctamente',
            'data'    => $grupo
        ], 201);
    }

    public function show(Grupo $grupo)
    {
        // Entregamos el grupo con sus relaciones cargadas
        return response()->json($grupo->load(['actividad', 'docente', 'ciclo', 'nivel']), 200);
    }

    public function update(UpdateGrupoRequest $request, Grupo $grupo)
    {
        
        $grupoActualizado = $this->grupoService->actualizar($grupo, $request->validated());

        return response()->json([
            'mensaje' => 'Grupo actualizado correctamente',
            'data'    => $grupoActualizado
        ], 200);
    }

    public function destroy(Grupo $grupo)
    {
        $this->grupoService->eliminar($grupo);
        
        return response()->json(['mensaje' => 'Grupo eliminado correctamente'], 200);
    }

    public function exportarExcel()
    {
        return $this->grupoService->exportarExcel();
    }
}