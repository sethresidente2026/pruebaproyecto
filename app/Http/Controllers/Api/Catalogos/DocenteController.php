<?php

namespace App\Http\Controllers\Api\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Docente; 
use App\Http\Requests\Docente\StoreDocenteRequest;
use App\Http\Requests\Docente\UpdateDocenteRequest;
use App\Services\Docente\DocenteService;

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

  
    public function show(string $id)
    {
         $docente=$this->docenteService->obtenerPorId($id);
         if(!$docente){
            return response()->json(['mensaje'=> 'Docente no Encontrado'],404);
         }
         return response()->json($docente,200);

    }
    public function update(UpdateDocenteRequest $request, string $id)
    {
        $docenteActualizado = $this->docenteService->actualizar($id, $request->validated());
        
        return response()->json([
            'mensaje' => 'Actualizado', 
            'data' => $docenteActualizado
        ]);
    }

    
    public function destroy(string $id)
    {
        $this->docenteService->eliminar($id);
        
        return response()->json(['mensaje' => 'Eliminado']);
    }

    public function exportarExcel() 
    {
        return $this->docenteService->exportarExcel();
    }
}