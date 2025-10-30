<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', function () {
    return redirect()->route('agenda.calendar');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Agenda
    Route::get('/agenda', [EventController::class, 'calendar'])->name('agenda.calendar');
    Route::get('/agenda/events', [EventController::class, 'getEvents'])->name('agenda.events');
    Route::resource('agenda/events', EventController::class)->except(['show']);
    Route::get('/agenda/events/{event}', [EventController::class, 'show'])->name('events.show');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        
        // Eventos del admin
        Route::get('/events', [EventController::class, 'adminEvents'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'createAdminEvent'])->name('events.create');
        Route::post('/events/store', [EventController::class, 'storeAdminEvent'])->name('events.store');

        // Gestión de usuarios
        Route::resource('users', UserController::class);

        // Gestión de roles (solo super-admin)
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});

// Ruta de prueba
Route::get('/test', function () {
    return "¡Admin LTE funciona correctamente!";
});