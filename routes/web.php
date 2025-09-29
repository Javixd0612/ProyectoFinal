<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ContactoController;
use Illuminate\Support\Facades\Route;

// HOME
Route::get('/', function () {
    return view('welcome');
});

// DASHBOARD USUARIO NORMAL
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// auth routes
require __DIR__ . '/auth.php';

// RUTAS PÚBLICAS
Route::view('/quienes-somos', 'quienes-somos')->name('quienes-somos');
Route::view('/contacto', 'contacto')->name('contacto');
Route::post('/contacto', [ContactoController::class, 'enviar'])->name('contacto.enviar');

// RUTAS QUE REQUIEREN AUTH
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reservas usuario
    Route::get('/reserva', [ReservaController::class, 'index'])->name('reserva.index');
    Route::post('/reserva', [ReservaController::class, 'store'])->name('reserva.store');
    Route::get('/reserva/{reserva}/edit', [ReservaController::class, 'edit'])->name('reserva.edit');
    Route::put('/reserva/{reserva}', [ReservaController::class, 'update'])->name('reserva.update');
    Route::delete('/reserva/{reserva}', [ReservaController::class, 'destroy'])->name('reserva.destroy');
    Route::post('/reserva/{reserva}/pay', [ReservaController::class, 'pay'])->name('reserva.pay');

    // Panel admin (prefijo + name 'admin.*')
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        // Dashboard admin (vista)
        Route::get('/', function () {
            abort_if(! auth()->check() || ! auth()->user()->isAdmin(), 403);
            return view('admin.dashboard');
        })->name('dashboard');

        // Reservas admin
        Route::get('/reservas', [ReservaController::class, 'adminIndex'])->name('reservas');
        Route::post('/reservas/{reserva}/mark-paid', [ReservaController::class, 'adminMarkPaid'])->name('reservas.mark_paid');
        Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

        // Consolas admin: crear, actualizar, eliminar
        Route::post('/consolas', [ReservaController::class, 'adminStoreConsola'])->name('consolas.store');
        // ruta para actualizar precio/nombre/descripcion (usada por los formularios)
        Route::post('/consolas/{consola}/update-price', [ReservaController::class, 'adminUpdateConsolaPrice'])->name('consolas.update_price');
        Route::delete('/consolas/{consola}', [ReservaController::class, 'adminDestroyConsola'])->name('consolas.destroy');
    });

});

use App\Http\Controllers\PreRegistroController;

Route::get('/register', [PreRegistroController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [PreRegistroController::class, 'store']);
Route::get('/verify-pre/{token}', [PreRegistroController::class, 'verify'])->name('verify.pre');
Route::get('/verify-pre/{token}', [PreRegistroController::class, 'verify'])->name('verify.pre');
