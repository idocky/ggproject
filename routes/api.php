<?php

use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('teams')->group(function (): void {
    Route::get('/', [TeamController::class, 'index']);
    Route::get('/{team}', [TeamController::class, 'show']);
    Route::post('/', [TeamController::class, 'create']);
    Route::match(['put', 'patch'], '/{team}', [TeamController::class, 'update']);
});

Route::prefix('players')->group(function (): void {
    Route::get('/', [PlayerController::class, 'index']);
    Route::get('/{player}', [PlayerController::class, 'show']);
    Route::post('/', [PlayerController::class, 'create']);
    Route::match(['put', 'patch'], '/{player}', [PlayerController::class, 'update']);
});

Route::prefix('matches')->group(function (): void {
    Route::get('/', [MatchController::class, 'index']);
    Route::get('/{match}', [MatchController::class, 'show']);
    Route::post('/', [MatchController::class, 'create']);
    Route::match(['put', 'patch'], '/{match}', [MatchController::class, 'update']);
});

Route::prefix('maps')->group(function (): void {
    Route::get('/', [MapController::class, 'index']);
    Route::get('/{map}', [MapController::class, 'show']);
    Route::post('/', [MapController::class, 'create']);
    Route::match(['put', 'patch'], '/{map}', [MapController::class, 'update']);
});
