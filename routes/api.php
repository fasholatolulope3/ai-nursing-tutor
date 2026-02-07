<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/scenarios', [App\Http\Controllers\Api\ScenarioController::class, 'index']);
        Route::get('/scenarios/{id}', [App\Http\Controllers\Api\ScenarioController::class, 'show']);

        Route::post('/simulations', [App\Http\Controllers\Api\SimulationController::class, 'start']);
        Route::get('/simulations/{id}', [App\Http\Controllers\Api\SimulationController::class, 'show']);
        Route::post('/simulations/{id}/chat', [App\Http\Controllers\Api\SimulationController::class, 'chat']);

        Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    });
});
