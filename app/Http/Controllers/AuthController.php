<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'tipo' => ['required', 'in:paciente,medico'], // campo oculto en el form
        ]);

        $guard = $credentials['tipo']; // 'paciente' o 'medico'

        if (Auth::guard($guard)->attempt(
        [
        'email' => $credentials['email'],
        'password' => $credentials['password'],

        ])) {

            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Bienvenido al sistema');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        // Detecta qué guard está autenticado
        $usuario = Auth::guard('paciente')->user() ?? Auth::guard('medico')->user();

        return view('dashboard', compact('usuario'));
    }

    public function logout(Request $request)
    {
        // Cierra sesión en ambos guards por seguridad
        Auth::guard('paciente')->logout();
        Auth::guard('medico')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('success', 'Sesión cerrada correctamente');
    }
}