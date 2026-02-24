<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Espacio;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEspacioRequest;
use App\Http\Requests\UpdateEspacioRequest;
use App\Services\EspacioService;
class EspacioController extends Controller
{
    protected $espacioService;
    public function __construct(EspacioService $espacioService)
    {
    $this->espacioService = $espacioService;
    }
    public function index()
    {
        return response()->json($this->espacioService->obtenerTodos());
    }

    
    public function store(StoreEspacioRequest $request)
{
    $espacio = $this->espacioService->crear($request->validated());

    return response()->json([
        'mensaje' => 'Espacio registrado correctamente',
        'data' => $espacio
    ], 201);
}
    public function show(Espacio $espacio)
    {
        return response()->json($espacio,200);
    }
    public function update(Espacio $espacio, UpdateEspacioRequest $request)
    {
        $espacioAct=$this->espacioService->actualizar($espacio,$request->validated());
        return response()->json([
            'mensaje' => 'Espacio actualizado',
            'data' => $espacioAct
        ], 200);
        
    }
 public function destroy(Espacio $espacio)
    {
     $this->espacioService->eliminar($espacio);
     return response()->json(['mensaje'=>'Espacio eliminado'],200);  
    }
}
