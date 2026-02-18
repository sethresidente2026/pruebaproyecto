<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horario;
class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Horario::with(['grupo','espacio'])->get(),200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'espacio_id' => 'required|exists:espacios,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required|date_format:H:i', 
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
    ]); 
    $empalme=Horario::where('espacio_id',$request->espacio_id)
    ->where('dia_semana',$request->dia_semana)
    ->where(function($query) use ($request) {
    $query->where('hora_inicio','<',$request->hora_fin);
    $query->where('hora_fin','>',$request->hora_inicio);
    })
    ->exists();
    if($empalme){
        return response()->json([
            'error'=>'Conflicto de Horario',
            'mensaje'=>'El espacio ya esta ocupado en ese rango de horas'
        ],409);
    }
    $horario=Horario::create($request->all());
    return response()->json([
        'mensaje'=>'Horario creado correctamente',
        'horario'=> $horario
    ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $horario = Horario::with(['grupo','espacio'])->find( $id );
        if(! $horario)return response()->json(['mensaje'=>'No encontrado'],404);
    
            return response()->json([$horario],200);
    }  


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      // 1. Buscamos el horario
        $horario = Horario::find($id);
        if (!$horario) {
            return response()->json(['mensaje' => 'No encontrado'], 404);
        }

        // 2. Validamos los datos nuevos
        $request->validate([
            'grupo_id' => 'exists:grupos,id',
            'espacio_id' => 'exists:espacios,id',
            'dia_semana' => 'string',
            'hora_inicio' => 'date_format:H:i',
            'hora_fin' => 'date_format:H:i|after:hora_inicio',
        ]);

        
        $nuevoEspacio = $request->espacio_id ?? $horario->espacio_id;
        $nuevoDia = $request->dia_semana ?? $horario->dia_semana;
        $nuevaInicio = $request->hora_inicio ?? $horario->hora_inicio;
        $nuevaFin = $request->hora_fin ?? $horario->hora_fin;

        $empalme = Horario::where('espacio_id', $nuevoEspacio)
            ->where('dia_semana', $nuevoDia)
            ->where('id', '!=', $id) // <--- ¡AQUÍ ESTÁ EL TRUCO! (Ignoramos este mismo registro)
            ->where(function ($query) use ($nuevaInicio, $nuevaFin) {
                $query->where('hora_inicio', '<', $nuevaFin)
                      ->where('hora_fin', '>', $nuevaInicio);
            })
            ->exists();

        if ($empalme) {
            return response()->json([
                'error' => 'Conflicto de horario',
                'mensaje' => 'El cambio provoca un empalme con otra clase existente.'
            ], 409);
        }

        // 4. Actualizamos
        $horario->update($request->all());

        return response()->json([
            'mensaje' => 'Horario actualizado correctamente',
            'data' => $horario
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $horario = Horario::find($id);
         if(! $horario)return response()->json(['mensaje'=>'No encontrado'],404);
        $horario->delete();
        return response()->json(['mensaje'=> 'Horario liberado'],200);
         }
}
