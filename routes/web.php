<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CalendarioController;

Route::get('/', function () {
    return redirect()->route('login.form');
});

Route::middleware('guest:paciente,medico')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:paciente,medico')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/citas/slots-disponibles', [CitaController::class, 'slotsDisponibles'])->name('citas.slots');
    Route::resource('citas', CitaController::class)->except(['destroy']);
    Route::patch('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::patch('/citas/{cita}/completar', [CitaController::class, 'completar'])->name('citas.completar');
});

Route::middleware('auth:medico')->group(function () {
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/calendario/dia', [CalendarioController::class, 'slotsPorDia'])->name('calendario.slots');
});