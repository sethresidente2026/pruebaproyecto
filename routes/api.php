<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EspacioController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\DocenteController;

// Prefijo automático /api/
Route::apiResource('espacios', EspacioController::class);
Route::apiResource('docentes', DocenteController::class);
Route::apiResource('horarios', HorarioController::class);
Route::apiResource('grupos', GrupoController::class);

Route::get('/actividades', function () { return \App\Models\Actividad::all(); });
Route::get('/ciclos', function () { return \App\Models\CicloEscolar::all(); });
Route::get('/niveles', function () { return \App\Models\Nivel::all(); });