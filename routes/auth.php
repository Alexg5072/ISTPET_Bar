<?php
// Reemplaza routes/auth.php con este contenido

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login',    [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login',   [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');

    Route::get('registro', [RegisterController::class, 'create'])->name('register');
    Route::post('registro',   [RegisterController::class, 'store'])->middleware('throttle:10,1');
    Route::get('registro/verificar-cedula', [RegisterController::class, 'verificarCedula'])
        ->middleware('throttle:30,1')
        ->name('register.check-cedula');
});

Route::middleware('auth')->group(function () {
    Route::post('logout',  [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});