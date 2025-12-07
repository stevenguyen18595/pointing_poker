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
Route::get('/games', [GameController::class, 'index']);
Route::post('/games', [GameController::class, 'store'])->middleware('throttle:5,1'); // 5 game creations per minute
Route::get('/games/{game}', [GameController::class, 'show']);
Route::put('/games/{game}', [GameController::class, 'update']);
Route::patch('/games/{game}', [GameController::class, 'update']);
Route::delete('/games/{game}', [GameController::class, 'destroy']);
Route::post('/games/join', [GameController::class, 'join'])->middleware('throttle:10,1'); // 10 joins per minute
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
