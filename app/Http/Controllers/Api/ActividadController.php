<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Actividad;
class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return response()->json(Actividad::orderBy('nombre', 'asc')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:actividades,nombre|max:100',
        ], [
            'nombre.unique' => 'Esta actividad ya está registrada.',
            'nombre.required' => 'El nombre es obligatorio.'
        ]);

        $actividad = Actividad::create($request->all());

        return response()->json([
            'mensaje' => 'Actividad creada con éxito',
            'actividad' => $actividad
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return response()->json(['mensaje' => 'Actividad no encontrada'], 404);
        }

        $request->validate([
            // Validamos que el nombre sea único, pero ignoramos el ID actual
            'nombre' => 'required|string|max:100|unique:actividades,nombre,' . $id,
        ], [
            'nombre.unique' => 'Ya existe otra actividad con este nombre.',
            'nombre.required' => 'El nombre no puede estar vacío.'
        ]);

        $actividad->update($request->all());

        return response()->json([
            'mensaje' => 'Actividad actualizada correctamente',
            'actividad' => $actividad
        ], 200);
    }

    public function destroy(string $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return response()->json(['mensaje' => 'Actividad no encontrada'], 404);
        }

        try {
            $actividad->delete();
            return response()->json(['mensaje' => 'Actividad eliminada correctamente'], 200);
        } catch (\Exception $e) {
            // Esto evita que se borre una actividad que ya tiene grupos asignados
            return response()->json([
                'mensaje' => 'No se puede eliminar: Esta actividad ya tiene grupos asignados.'
            ], 422);
        }
    }
    
}
