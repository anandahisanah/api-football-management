<?php

use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('team')->name('team')->group(function () {
    Route::get('/', [TeamController::class, 'index']);
    Route::get('/show', [TeamController::class, 'show'])->name('.show');
    Route::post('/store', [TeamController::class, 'store'])->name('.store');
    Route::post('/update', [TeamController::class, 'update'])->name('.update');
    Route::delete('/delete/{id}', [TeamController::class, 'delete'])->name('.delete');
});

Route::prefix('player')->name('player')->group(function () {
    Route::get('/', [PlayerController::class, 'index']);
    Route::get('/show', [PlayerController::class, 'show'])->name('.show');
    Route::post('/store', [PlayerController::class, 'store'])->name('.store');
    Route::post('/update', [PlayerController::class, 'update'])->name('.update');
    Route::delete('/delete/{id}', [PlayerController::class, 'delete'])->name('.delete');
});

Route::prefix('game')->name('game')->group(function () {
    Route::get('/', [GameController::class, 'index']);
    Route::get('/show', [GameController::class, 'show'])->name('.show');
    Route::post('/store', [GameController::class, 'store'])->name('.store');
    Route::post('/update', [GameController::class, 'update'])->name('.update');
    Route::delete('/delete/{id}', [GameController::class, 'delete'])->name('.delete');
});

Route::prefix('score')->name('score')->group(function () {
    Route::post('/store', [ScoreController::class, 'store'])->name('.store');
    Route::delete('/delete/{id}', [ScoreController::class, 'delete'])->name('.delete');
});

Route::prefix('report')->name('report')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
});
