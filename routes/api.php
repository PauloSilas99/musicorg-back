<?php

use App\Http\Controllers\Auth\BandaAuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\MusicoEventoController;
use App\Http\Controllers\MusicaEventoController;
use Illuminate\Support\Facades\Route;

// Rotas públicas de autenticação
Route::post('/register', [BandaAuthController::class, 'register']);
Route::post('/login', [BandaAuthController::class, 'login']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:bandas')->group(function () {
    // Autenticação
    Route::post('/logout', [BandaAuthController::class, 'logout']);
    Route::get('/me', [BandaAuthController::class, 'me']);

    // CRUD de Eventos
    Route::apiResource('eventos', EventoController::class);

    // Gerenciamento de Músicos (sub-recurso de Eventos)
    Route::prefix('eventos/{eventoId}/musicos')->name('eventos.musicos.')->group(function () {
        Route::get('/', [MusicoEventoController::class, 'index'])->name('index');
        Route::post('/', [MusicoEventoController::class, 'store'])->name('store');
        Route::get('/{musicoId}', [MusicoEventoController::class, 'show'])->name('show');
        Route::put('/{musicoId}', [MusicoEventoController::class, 'update'])->name('update');
        Route::delete('/{musicoId}', [MusicoEventoController::class, 'destroy'])->name('destroy');
    });

    // Gerenciamento de Músicas (sub-recurso de Eventos)
    Route::prefix('eventos/{eventoId}/musicas')->name('eventos.musicas.')->group(function () {
        Route::get('/', [MusicaEventoController::class, 'index'])->name('index');
        Route::post('/', [MusicaEventoController::class, 'store'])->name('store');
        Route::post('/reorder', [MusicaEventoController::class, 'reorder'])->name('reorder');
        Route::get('/{musicaId}', [MusicaEventoController::class, 'show'])->name('show');
        Route::put('/{musicaId}', [MusicaEventoController::class, 'update'])->name('update');
        Route::delete('/{musicaId}', [MusicaEventoController::class, 'destroy'])->name('destroy');
    });
});

