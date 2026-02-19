<?php

namespace App\Http\Controllers\Api;

use App\Exports\DocentesExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Docente;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        // Esto le manda la lista real a Vue al cargar la página
        return Docente::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {$request->validate([
        'nombre'=> 'required|string|max:50',
            'apellidos'=> 'required|string|max:50',
            'email'=> 'required|email|unique:docentes,email',
            'estatus' => 'in:Activo,Inactivo,Baja Temporal'
    ]);
    $docente = Docente::create($request->all());
    return response()->json([
        'mensaje'=> 'Docente Registrado correctamente',
        'data'=> $docente
        ],200);
            }

    public function show(string $id)
    {
        $docente = Docente::find($id);
        if (!$docente) return response()->json(['mensaje'=> 'No encontrado'],404);
        return response()->json($docente,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $docente = Docente::find($id);
        if (!$docente) return response()->json(['mensaje'=> 'No encontrado'],404);
        $request->validate([
            'nombre'=> 'string|max:50',
            'email'=> 'email|unique:docentes,email,'.$id]);
    $docente->update($request->all());
    return response()->json(['mensaje'=>'Actualizado','data'=>$docente],200);
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $docente = Docente::find($id);
      if (!$docente) return response()->json(['mensaje'=> 'No encontrado'],404);
      $docente->delete();
      return response()->json(['mensaje'=>'Eliminado'],200);
    }
   public function exportarExcel() 
    {
        
        $fecha = Carbon::now()->format('d_m_Y');
       
        $nombreArchivo = "Reporte_Docentes_UGM_{$fecha}.xlsx";

       
        return Excel::download(new DocentesExport, $nombreArchivo);
    }
}
