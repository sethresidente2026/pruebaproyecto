<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Espacio;
use Illuminate\Http\Request;

class EspacioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([Espacio::all(),200]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // ⚠️ ASÍ DEBE ESTAR (Solo el array):
    $request->validate([
        'nombre' => 'required|string|max:100',
        'capacidad' => 'required|integer|min:1'
    ]);

    $espacio = Espacio::create($request->all());

    return response()->json([
        'mensaje' => 'Espacio registrado correctamente',
        'data' => $espacio
    ], 201);
}

   
    public function show(string $id)
    {
        $espacio=Espacio::find($id);
        if(!$espacio){
            return response()->json(['mensaje'=>'Espacio no encontrado'],404);
        }return response()->json($espacio,200);
    }

    public function update(Request $request, string $id)
    {
        $espacio = Espacio::find($id);
        if (!$espacio) return response()->json(['mensaje' => 'No encontrado'], 404);

        $espacio->update($request->all());

        return response()->json([
            'mensaje' => 'Espacio actualizado',
            'data' => $espacio
        ], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $espacio=Espacio::find($id);
        if(!$espacio) return response()->json(['mensaje'=> 'No encontrado'],404);
        $espacio->delete();
        return response()->json(['mensaje'=>'Espacio eliminado'],202);
    }
}
