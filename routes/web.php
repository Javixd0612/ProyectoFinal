<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\ReservaApiController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reserva', function () {
    return view('reserva.index');
})->name('reserva');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\Auth\PasswordResetLinkController;

Route::middleware('guest')->group(function() {
    // Mostrar formulario para ingresar correo
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Enviar correo con enlace de restablecimiento
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
});


// ==========================
// RUTAS SOLO PARA ADMIN
// ==========================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::get('/reservas', fn() => view('admin.reservas'))->name('admin.reservas');
    Route::get('/usuarios', fn() => view('admin.usuarios'))->name('admin.usuarios');
    Route::get('/configuraciones', fn() => view('admin.configuraciones'))->name('admin.configuraciones');
});
