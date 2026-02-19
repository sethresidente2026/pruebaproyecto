<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EspacioController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\DocenteController;
use App\Http\Controllers\Api\ReporteController;
// Prefijo automático /api/
Route::get('docentes/exportar', [DocenteController::class, 'exportarExcel']);


Route::get('reportes/docentes', [ReporteController::class, 'exportarDocentes']);
Route::get('reportes/grupos', [ReporteController::class, 'exportarGrupos']);
Route::get('reportes/horarios', [ReporteController::class, 'exportarHorarios']);
Route::get('reporte-general', [ReporteController::class, 'reporteGeneral']);

// 2. Rutas generales
Route::apiResource('docentes', DocenteController::class);
Route::apiResource('espacios', App\Http\Controllers\Api\EspacioController::class);
Route::apiResource('grupos', App\Http\Controllers\Api\GrupoController::class);
Route::apiResource('horarios', App\Http\Controllers\Api\HorarioController::class);

// Auxiliares
Route::get('/actividades', function () { return \App\Models\Actividad::all(); });
Route::get('/ciclos', function () { return \App\Models\CicloEscolar::all(); });
Route::get('/niveles', function () { return \App\Models\Nivel::all(); });
Route::get('reporte-general', [ReporteController::class, 'reporteGeneral']);