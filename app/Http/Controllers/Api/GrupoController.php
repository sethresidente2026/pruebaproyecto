<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grupo;
class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
    {
        // Con "with" le exigimos a Laravel que incluya los datos de esas 4 tablas
        return Grupo::with(['docente', 'actividad', 'ciclo', 'nivel'])->get();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'=>'required|string',
            'cupo_maximo'=> 'required|integer',
            'actividad_id'=> 'required|exists:actividades,id',
            'docente_id'=> 'required|exists:docentes,id',
            'ciclo_id'=>'required|exists:ciclos_escolares,id',
            'nivel_id'=> 'nullable|exists:niveles,id'

            ]);
            $grupo = Grupo::create($request->all());
            return response()->json([
            'mensaje'=> 'Grupo Creado con exito',   
            'grupo'=> $grupo
            ],200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $grupo= Grupo::with(['actividad','docente','ciclo','horarios.espacio'])->find( $id );
        if(!$grupo){
            return response()->json(['mensaje'=>'Grupo no encontrado'],404);
       }return response()->json($grupo,200);
     }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      $grupo=Grupo::find($id);
      if(!$grupo)  return response()->json(['mensaje'=> 'Grupo no encontrado'],404);
    $grupo->update($request->all());
    return response()->json([
        'mensaje'=>'Grupo Actualizado',
        'grupo'=>$grupo
    ],200);
       
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $grupo =Grupo::find($id);
        if(!$grupo) return response()->json(['mensaje'=> 'Grupo no encontrad'],200);
        $grupo->delete();
        return response()->json(['mensaje'=> 'Grupo eliminado correctamente'],200);
    }
}
