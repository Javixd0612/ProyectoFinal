<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;

// Home / welcome
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (protegida)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// RUTAS DE AUTENTICACIÓN (auth.php)
require __DIR__.'/auth.php';

// RUTAS PÚBLICAS
Route::view('/quienes-somos', 'quienes-somos')->name('quienes-somos');
Route::view('/contacto', 'contacto')->name('contacto');

// RUTAS QUE REQUIEREN AUTH
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RESERVAS - usuario: ver + crear
    Route::get('/reserva', [ReservaController::class, 'index'])->name('reserva.index');
    Route::post('/reserva', [ReservaController::class, 'store'])->name('reserva.store');
});

// RUTAS SOLO PARA ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');

    // Ver todas las reservas (controller)
    Route::get('/reservas', [ReservaController::class, 'adminIndex'])->name('admin.reservas');
    // Eliminar reserva (admin)
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('admin.reservas.destroy');

    Route::get('/usuarios', fn() => view('admin.usuarios'))->name('admin.usuarios');
    Route::get('/configuraciones', fn() => view('admin.configuraciones'))->name('admin.configuraciones');
});

use App\Http\Controllers\ContactoController;

Route::post('/contacto', [ContactoController::class, 'enviar'])->name('contacto.enviar');
