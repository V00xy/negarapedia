<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NegaraController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\MapController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kuis Tebak Bendera (Mhs 1)
    Route::get('/kuis',           [QuizController::class, 'index'])->name('kuis.index');
    Route::get('/kuis/questions', [QuizController::class, 'getQuestions'])->name('kuis.questions');
    Route::post('/kuis/result',   [QuizController::class, 'saveResult'])->name('kuis.result');
    Route::delete('/kuis/{id}',   [QuizController::class, 'destroy'])->name('kuis.destroy');

    // Leaderboard (Mhs 1)
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // AI Chat Negara
    Route::get('/ai-chat',  [AIChatController::class, 'index'])->name('ai-chat.index');
    Route::post('/ai-chat', [AIChatController::class, 'chat'])->name('ai-chat.chat');

    // Peta Interaktif
    Route::get('/peta', [MapController::class, 'index'])->name('peta.index');

    // Pencarian & Favorit Negara (Mhs 2)
    Route::get('/negara',                     [NegaraController::class, 'index'])->name('negara.index');
    Route::get('/negara/search',              [NegaraController::class, 'search'])->name('negara.search');
    Route::get('/negara/favorites',           [NegaraController::class, 'favorites'])->name('negara.favorites');
    Route::post('/negara/favorites',          [NegaraController::class, 'storeFavorite'])->name('negara.favorites.store');
    Route::get('/negara/favorites/{id}/edit', [NegaraController::class, 'editFavorite'])->name('negara.favorites.edit');
    Route::put('/negara/favorites/{id}',      [NegaraController::class, 'updateFavorite'])->name('negara.favorites.update');
    Route::delete('/negara/favorites/{id}',   [NegaraController::class, 'destroyFavorite'])->name('negara.favorites.destroy');
});