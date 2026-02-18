<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
  //  return $request->user();
//})->middleware('auth:sanctum');
use App\Http\Controllers\Api\EspacioController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\DocenteController;

Route::apiResource('espacios', EspacioController::class);
Route::apiResource('docentes',DocenteController::class);

Route::apiResource('horarios',HorarioController::class);
Route::apiResource('grupos', GrupoController::class);
