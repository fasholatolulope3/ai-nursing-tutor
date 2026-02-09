<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('LandingContext');
})->name('home');

Route::get('/ai', fn() => view('gemini'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('dashboard/settings', function () {
        return Inertia::render('Settings');
    })->name('dashboard.settings');

    Route::get('dashboard/scenarios', function () {
        return Inertia::render('Dashboard/Scenarios');
    })->name('dashboard.scenarios');

    Route::get('dashboard/scenarios/{id}', [\App\Http\Controllers\ScenarioController::class, 'show'])->name('dashboard.scenarios.show');

    Route::get('dashboard/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('dashboard.history');
    Route::delete('dashboard/history/{id}', [\App\Http\Controllers\HistoryController::class, 'destroy'])->name('dashboard.history.destroy');

    Route::get('dashboard/performance', function () {
        return Inertia::render('Dashboard/Performance');
    })->name('dashboard.performance');

    Route::get('dashboard/guidelines', function () {
        return Inertia::render('Dashboard/Guidelines');
    })->name('dashboard.guidelines');

    Route::get('dashboard/exam-prep', function () {
        return Inertia::render('Dashboard/ExamPrep');
    })->name('dashboard.exam-prep');

    Route::get('dashboard/achievements', function () {
        return Inertia::render('Dashboard/Achievements');
    })->name('dashboard.achievements');

    Route::get('dashboard/help', function () {
        return Inertia::render('Dashboard/Help');
    })->name('dashboard.help');
});

Route::get('/simulation/{id}', function ($id) {
    return Inertia::render('SimulationContext', ['sessionId' => (int) $id]);
})->middleware(['auth', 'verified'])->name('simulation.show');

require __DIR__ . '/settings.php';
