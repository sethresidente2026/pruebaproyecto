<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EspacioController;
use App\Http\Controllers\Api\GrupoController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\DocenteController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');