<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importa tus controladores (Ojo con el de Asistencia)
use App\Http\Controllers\Api\Catalogos\DocenteController;
use App\Http\Controllers\Api\Catalogos\EspacioController;
use App\Http\Controllers\Api\Operacion\GrupoController;
use App\Http\Controllers\Api\Operacion\HorarioController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Operacion\AsistenciaController;
use App\Http\Controllers\Api\Catalogos\ActividadController;

// RUTAS PÚBLICAS
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('logout', [AuthController::class, 'logout']);
    
  
    Route::get('reportes/pagos', [AsistenciaController::class, 'exportarPagos']);
    Route::get('reporte-general', [ReporteController::class, 'reporteGeneral']);
    Route::get('reporte-pdf', [ReporteController::class, 'generarPDF']);
Route::get('reportes/grupos', [ReporteController::class, 'exportarGrupos']);
Route::get('reportes/horarios', [ReporteController::class, 'exportarHorarios']);
Route::get('reportes/docentes', [ReporteController::class, 'exportarDocentes']);
   
    Route::apiResource('docentes', DocenteController::class);
    Route::apiResource('espacios', EspacioController::class);
    Route::apiResource('grupos', GrupoController::class);
    Route::apiResource('horarios', HorarioController::class);
    Route::apiResource('asistencias', AsistenciaController::class);
   Route::apiResource('actividades', ActividadController::class)->parameters([
    'actividades' => 'actividad' 
]);

    
    Route::get('/ciclos', function () { return \App\Models\CicloEscolar::all(); });
    Route::get('/niveles', function () { return \App\Models\Nivel::all(); });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});