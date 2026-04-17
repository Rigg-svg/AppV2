<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
});