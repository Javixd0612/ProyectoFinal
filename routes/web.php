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




Route::get('/', function () {
    return view('welcome');
});


// Dashboard home (puede ser vista principal protegida)
Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class,'index'])->name('home');

    // Reservas - usuario
    Route::get('/reservas/create', [ReservaController::class,'create'])->name('reservas.create');
    Route::post('/reservas', [ReservaController::class,'store'])->name('reservas.store');
    Route::get('/mis-reservas', [ReservaController::class,'misReservas'])->name('reservas.mis');

    // Mostrar todas (para admin y lectura por otros usuarios)
    Route::get('/reservas', [ReservaController::class,'index'])->name('reservas.index');

    // Cancelar (usuario o admin)
    Route::post('/reservas/{id}/cancelar', [ReservaController::class,'cancelar'])->name('reservas.cancelar');

    // Admin "soft cancel"
    Route::delete('/reservas/{id}', [ReservaController::class,'destroy'])->name('reservas.destroy');
});

// MercadoPago webhook (public)
Route::post('/mp/webhook', [MercadoPagoController::class,'webhook'])->name('mp.webhook');

// API para FullCalendar
Route::get('/api/reservas/events', [ReservaApiController::class,'events'])->name('api.reservas.events');

// Simulación de pago (solo si no tienes MercadoPago token)
Route::get('/reservas/simulate/{id}', [App\Http\Controllers\ReservaController::class, 'simulatePayment'])->name('reservas.simulate');
Route::post('/reservas/simulate/{id}/confirm', [App\Http\Controllers\ReservaController::class, 'simulateConfirm'])->name('reservas.simulate.confirm');

use App\Http\Controllers\Auth\PasswordResetLinkController;

Route::middleware('guest')->group(function() {
    // Mostrar formulario para ingresar correo
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Enviar correo con enlace de restablecimiento
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
});



