<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Asegúrate de que esta línea esté presente

class AuthController extends Controller
{
    // app/Http/Controllers/Api/AuthController.php

// app/Http/Controllers/Api/AuthController.php

public function login(Request $request) 
{
    $credenciales = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credenciales)) {
        // Esta línea ya no fallará si activaste statefulApi() arriba
        $request->session()->regenerate(); 

        return response()->json([
            'mensaje' => 'Bienvenido',
            'usuario' => Auth::user()
        ], 200);
    }

    return response()->json(['mensaje' => 'Credenciales inválidas'], 401);
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['mensaje' => 'Sesión cerrada'], 200);
    }
}