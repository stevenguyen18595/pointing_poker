<?php

use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\GameStatusController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PointValueController;
use App\Http\Controllers\Api\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Game Status Routes
Route::get('/game-statuses', [GameStatusController::class, 'index']);
Route::get('/game-statuses/{gameStatus}', [GameStatusController::class, 'show']);

// Point Values Routes
Route::get('/point-values', [PointValueController::class, 'index']);
Route::get('/point-values/{pointValue}', [PointValueController::class, 'show']);

// Game Routes
Route::apiResource('games', GameController::class);
Route::post('/games/join', [GameController::class, 'join']);
Route::patch('/games/{game}/status', [GameController::class, 'updateStatus']);

// Player Routes (nested under games)
Route::prefix('games/{game}')->group(function () {
    Route::get('/players', [PlayerController::class, 'index']);
    Route::get('/players/{player}', [PlayerController::class, 'show']);
    Route::patch('/players/{player}', [PlayerController::class, 'update']);
    Route::delete('/players/{player}', [PlayerController::class, 'destroy']);
    Route::post('/players/{player}/activity', [PlayerController::class, 'updateActivity']);
    
    // Vote Routes (nested under games)
    Route::get('/votes', [VoteController::class, 'index']);
    Route::post('/votes', [VoteController::class, 'store']);
    Route::get('/votes/{player}', [VoteController::class, 'show']);
    Route::delete('/votes/{player}', [VoteController::class, 'destroy']);
    Route::post('/reveal', [VoteController::class, 'reveal']);
    Route::delete('/votes', [VoteController::class, 'reset']);
});

// Vote Routes (independent)
Route::patch('/votes/{vote}', [VoteController::class, 'update']);