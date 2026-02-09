<?php

use App\Models\SimulationHistory;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$user = User::first();

if (!$user) {
    echo "No user found.\n";
    exit(1);
}

SimulationHistory::create([
    'user_id' => $user->id,
    'scenario_title' => 'Sepsis Scenario',
    'scenario_type' => 'Clinical',
    'difficulty' => 'Hard',
    'score_or_outcome' => '85/100',
    'duration_minutes' => 15
]);

echo "Seeded history for user: " . $user->email . "\n";
