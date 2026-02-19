<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// 1. Rutas de autenticación (CON sesión)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// 2. Ruta para cargar tu App de Vue (DEBE IR AL FINAL)
Route::get('/{any}', function () {
    return view('welcome'); 
})->where('any', '.*');