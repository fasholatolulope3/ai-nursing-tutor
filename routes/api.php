<?php

use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProfileController;

Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // Route::post('/simulations', [App\Http\Controllers\Api\V1\SimulationController::class, 'store']); // Temp exposed

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('scenarios', \App\Http\Controllers\Api\V1\ScenarioController::class)->only(['index', 'show']);

        // Simulation routes
        Route::get('/simulations', [App\Http\Controllers\Api\V1\SimulationController::class, 'index']); // List user sessions
        Route::post('/simulations/start', [App\Http\Controllers\Api\V1\SimulationController::class, 'start']);
        Route::post('/simulations', [App\Http\Controllers\Api\V1\SimulationController::class, 'store']);
        // Route::post('/simulations/clinical-query', [App\Http\Controllers\Api\V1\SimulationController::class, 'handleClinicalQuery']); // Moved outside for access
        Route::get('/simulations/{id}', [App\Http\Controllers\Api\V1\SimulationController::class, 'show']);
        Route::post('/simulations/{id}/chat', [App\Http\Controllers\Api\V1\SimulationController::class, 'chat']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    });

    // Gemini Integration
    Route::post('/gemini', [\App\Http\Controllers\GeminiController::class, 'generate']);

    // Clinical Query (Public Access for Dashboard)
    Route::post('/simulations/clinical-query', [App\Http\Controllers\Api\V1\SimulationController::class, 'handleClinicalQuery']);
    Route::get('/simulations/clinical-query/last', [App\Http\Controllers\Api\V1\SimulationController::class, 'getLastInteraction']);
    Route::get('/simulations/clinical-query/{id}', [App\Http\Controllers\Api\V1\SimulationController::class, 'getInteraction']);

    // Reference Library
    Route::get('/references', [App\Http\Controllers\Api\V1\ReferenceController::class, 'index']);

    // Recommendations
    Route::post('/recommendations', [App\Http\Controllers\Api\V1\RecommendationsController::class, 'index']);

    // Scenario Generation (Factory)
    Route::post('/scenarios/generate', [\App\Http\Controllers\Api\V1\ScenarioController::class, 'generate']);
});
