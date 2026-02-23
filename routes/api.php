<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importa aquí tus controladores
use App\Http\Controllers\Api\DocenteController;
use App\Http\Controllers\Api\EspacioController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\AuthController; // Asumiendo que tienes uno para el Login
use App\Http\Controllers\AsistenciaController;
//RUTAS PÚBLICAS (Sin candado)

Route::post('login', [AuthController::class, 'login']);





Route::middleware('auth:sanctum')->group(function () {
    
    
    Route::post('logout', [AuthController::class, 'logout']);
    
   
    Route::get('reportes/docentes', [ReporteController::class, 'exportarDocentes']);
    Route::get('reportes/grupos', [ReporteController::class, 'exportarGrupos']);
    Route::get('reportes/horarios', [ReporteController::class, 'exportarHorarios']);
    Route::get('reporte-general', [ReporteController::class, 'reporteGeneral']);
    Route::get('reporte-pdf', [ReporteController::class, 'generarPDF']);
    Route::get('reportes/pagos', [AsistenciaController::class, 'exportarPagos']);
   
    Route::apiResource('docentes', DocenteController::class);
    Route::apiResource('espacios', EspacioController::class);
    Route::apiResource('grupos', GrupoController::class);
    Route::apiResource('horarios', HorarioController::class);
    Route::apiResource('asistencias', AsistenciaController::class);
 
    Route::get('/actividades', function () { return \App\Models\Actividad::all(); });
    Route::get('/ciclos', function () { return \App\Models\CicloEscolar::all(); });
    Route::get('/niveles', function () { return \App\Models\Nivel::all(); });

    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});