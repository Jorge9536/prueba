<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', [HomeController::class, 'index']);

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta home para AdminLTE
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Agenda principal
    Route::get('/agenda', [EventController::class, 'calendar'])->name('agenda.calendar');
    Route::get('/agenda/get-events', [EventController::class, 'getEvents'])->name('agenda.events');
    Route::get('/agenda/upcoming-events', [EventController::class, 'getUpcomingEvents'])->name('agenda.upcoming-events');
    Route::resource('agenda/events', EventController::class)->except(['show']);
    Route::get('/agenda/events/{event}', [EventController::class, 'show'])->name('events.show');

    // Gestión de recordatorios
    Route::post('/agenda/events/{event}/toggle-reminder', [EventController::class, 'toggleReminder'])->name('events.toggle-reminder');

    // Administración
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Eventos del admin
        Route::get('/events', [EventController::class, 'adminEvents'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'createAdminEvent'])->name('events.create');
        Route::post('/events/store', [EventController::class, 'storeAdminEvent'])->name('events.store');

        // Gestión de usuarios
        Route::resource('users', UserController::class);

        // Gestión de roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});