<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horario;
use App\Models\Grupo;
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
        // 1. Validamos que lleguen los datos correctos
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'espacio_id' => 'required|exists:espacios,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $dia = $request->dia_semana;
        $inicio = $request->hora_inicio;
        $fin = $request->hora_fin;

        // ==========================================
        // BARRERA 1: EMPALME DE ESPACIO
        // ==========================================
        $empalmeEspacio = Horario::where('espacio_id', $request->espacio_id)
            ->where('dia_semana', $dia)
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('hora_inicio', '<', $fin)
                      ->where('hora_fin', '>', $inicio);
            })->exists();

        if ($empalmeEspacio) {
            return response()->json(['mensaje' => 'El ESPACIO ya está ocupado en ese horario.'], 409);
        }

        
        $empalmeGrupo = Horario::where('grupo_id', $request->grupo_id)
            ->where('dia_semana', $dia)
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('hora_inicio', '<', $fin)
                      ->where('hora_fin', '>', $inicio);
            })->exists();

        if ($empalmeGrupo) {
            return response()->json(['mensaje' => 'El GRUPO ya tiene una actividad asignada a esa hora.'], 409);
        }

        $grupoNuevo = Grupo::find($request->grupo_id);

        
        $empalmeDocente = Horario::whereHas('grupo', function($query) use ($grupoNuevo) {
                $query->where('docente_id', $grupoNuevo->docente_id);
            })
            ->where('dia_semana', $dia)
            ->where(function ($query) use ($inicio, $fin) {
                $query->where('hora_inicio', '<', $fin)
                      ->where('hora_fin', '>', $inicio);
            })->exists();

        if ($empalmeDocente) {
            return response()->json(['mensaje' => 'El DOCENTE ya imparte clases a otro grupo en ese horario.'], 409);
        }

        $horario = Horario::create($request->all());

        return response()->json([
            'mensaje' => 'Horario asignado correctamente',
            'data' => $horario
        ], 201);
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
