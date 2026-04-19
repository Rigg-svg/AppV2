<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;

// Redirige la raíz al login
Route::get('/', function () {
    return redirect()->route('login.form');
});

// Rutas públicas (solo si NO está autenticado)
Route::middleware('guest:paciente,medico')->group(function () {

    Route::get('/login', [AuthController::class , 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class , 'login'])->name('login');
});

// Rutas protegidas (debe estar autenticado como paciente O médico)
Route::middleware('auth:paciente,medico')->group(function () {

    Route::get('/dashboard', [AuthController::class , 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

    // Rutas de citas
    Route::resource('citas', CitaController::class)->except(['destroy']);
    Route::patch('/citas/{cita}/cancelar', [CitaController::class , 'cancelar'])->name('citas.cancelar');
});

// resources/
// └── views/
//     └── citas/
//         ├── index.blade.php    → citas.index
//         ├── create.blade.php   → citas.create
//         ├── show.blade.php     → citas.show
//         └── edit.blade.php     → citas.edit

// Route::get('/citas',                [CitaController::class, 'index']);
// Route::get('/citas/create',         [CitaController::class, 'create']);
// Route::post('/citas',               [CitaController::class, 'store']);
// Route::get('/citas/{cita}',         [CitaController::class, 'show']);
// Route::get('/citas/{cita}/edit',    [CitaController::class, 'edit']);
// Route::put('/citas/{cita}',         [CitaController::class, 'update']);
// Route::delete('/citas/{cita}',      [CitaController::class, 'destroy']);

// <!-- Enlace a la lista -->
// <a href="{{ route('citas.index') }}">Ver citas</a>

// <!-- Enlace al detalle -->
// <a href="{{ route('citas.show', $cita->id) }}">Ver detalle</a>

// <!-- Formulario de edición -->
// <form action="{{ route('citas.update', $cita->id) }}" method="POST">
//     @method('PUT')
//     @csrf
// </form>