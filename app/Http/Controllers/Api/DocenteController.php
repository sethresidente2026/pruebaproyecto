<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Docente; 
use App\Http\Requests\StoreDocenteRequest;
use App\Http\Requests\UpdateDocenteRequest;
use App\Services\DocenteService;

class DocenteController extends Controller
{
    protected $docenteService;

    public function __construct(DocenteService $docenteService)
    {
        $this->docenteService = $docenteService;
    }

    public function index()
    {
        return response()->json($this->docenteService->obtenerTodos());
    }

    public function store(StoreDocenteRequest $request)
    {
        $docente = $this->docenteService->crear($request->validated());
        return response()->json(['mensaje' => 'Registrado', 'data' => $docente], 201);
    }

  
    public function show(Docente $docente)
    {
        return response()->json($docente);
    }

  
    public function update(UpdateDocenteRequest $request, Docente $docente)
    {
        $docenteActualizado = $this->docenteService->actualizar($docente, $request->validated());
        return response()->json(['mensaje' => 'Actualizado', 'data' => $docenteActualizado]);
    }

    
    public function destroy(Docente $docente)
    {
        $this->docenteService->eliminar($docente);
        return response()->json(['mensaje' => 'Eliminado']);
    }

    public function exportarExcel() 
    {
        return $this->docenteService->exportarExcel();
    }
}