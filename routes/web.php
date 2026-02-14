<?php

use App\Http\Controllers\ShowController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialNetworkController;
use Inertia\Inertia;

// LA CORRECCIÓN: Ahora la raíz usa tu controlador para cargar los datos
Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::get('/admin', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas privadas para el Panel de Admin
Route::middleware(['auth', 'verified'])->group(function () {
    
    // El Dashboard principal
    Route::get('/admin', [ShowController::class, 'adminIndex'])->name('dashboard');
    
    // Gestión de Shows
    Route::get('/admin/shows/create', [ShowController::class, 'create'])->name('shows.create');
    Route::post('/admin/shows', [ShowController::class, 'store'])->name('shows.store');
    
    // Futuras rutas de edición y borrado irían acá:
    // Route::get('/admin/shows/{show}/edit', [ShowController::class, 'edit'])->name('shows.edit');
    // Route::patch('/admin/shows/{show}', [ShowController::class, 'update'])->name('shows.update');

    Route::get('/admin/shows/{show}/edit', [ShowController::class, 'edit'])->name('shows.edit');
    Route::patch('/admin/shows/{show}', [ShowController::class, 'update'])->name('shows.update');

    Route::patch('/admin/shows/{show}/toggle-sold-out', [ShowController::class, 'toggleSoldOut'])->name('shows.toggle-sold-out');

    Route::delete('/admin/shows/{show}', [ShowController::class, 'destroy'])->name('shows.destroy');

    // ABM de Redes
    Route::get('/admin/redes', [SocialNetworkController::class, 'index'])->name('redes.index');
    Route::post('/admin/redes', [SocialNetworkController::class, 'store'])->name('redes.store');
    Route::patch('/admin/redes/{socialNetwork}', [SocialNetworkController::class, 'update'])->name('redes.update');
    Route::delete('/admin/redes/{socialNetwork}', [SocialNetworkController::class, 'destroy'])->name('redes.destroy');
});

require __DIR__.'/auth.php';