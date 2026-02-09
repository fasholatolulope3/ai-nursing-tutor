<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\AiInteraction;
use App\Models\SimulationSession;
use Illuminate\Http\Request;
use App\Http\Controllers\HistoryController;
use Inertia\Response;

echo "--- START HISTORY VERIFICATION ---\n";

try {
    $user = User::first();
    if (!$user) {
        $user = User::factory()->create();
    }
    echo "User ID: {$user->id}\n";

    // 1. seed dat
    // Ensure we have at least one AI chat
    if (AiInteraction::where('user_id', $user->id)->count() === 0) {
        AiInteraction::create([
            'user_id' => $user->id,
            'prompt' => 'History Test Prompt',
            'ai_response' => ['answer' => 'History Test Answer']
        ]);
        echo "Created dummy AI Interaction.\n";
    }

    // Ensure we have at least one Simulation Session
    if (SimulationSession::where('user_id', $user->id)->count() === 0) {
        $scenario = \App\Models\ClinicalScenario::first() ?? \App\Models\ClinicalScenario::factory()->create();
        SimulationSession::create([
            'user_id' => $user->id,
            'scenario_id' => $scenario->id,
            'status' => 'completed',
            'started_at' => now()->subDay(),
            'score' => 85
        ]);
        echo "Created dummy Simulation Session.\n";
    }

    // 2. Mock Request
    $request = Request::create('/dashboard/history', 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    // 3. Call Controller
    $controller = app()->make(HistoryController::class);
    $response = $controller->index($request);

    // 4. Inspect Response (Inertia Response)
    // Inertia response is not a simple JSON. accessing props is tricky in raw script without rendering.
    // However, we can check the 'props' property if accessible, or using reflection.

    $reflection = new ReflectionClass($response);
    $propsProperty = $reflection->getProperty('props');
    $propsProperty->setAccessible(true);
    $props = $propsProperty->getValue($response);

    $historyData = $props['history']['data'];

    echo "Found " . count($historyData) . " history items.\n";

    $hasAiChat = false;
    $hasSimulation = false;

    foreach ($historyData as $item) {
        echo "- [{$item['type']}] {$item['title']} ({$item['date']})\n";
        if ($item['type'] === 'ai_chat')
            $hasAiChat = true;
        if ($item['type'] === 'simulation')
            $hasSimulation = true;
    }

    if ($hasAiChat && $hasSimulation) {
        echo "SUCCESS: Both types present in history.\n";
    } else {
        echo "FAILURE: Missing types.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "--- END VERIFICATION ---\n";
