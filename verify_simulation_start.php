<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ClinicalScenario;
use App\Http\Controllers\Api\V1\SimulationController;
use Illuminate\Http\Request;

echo "--- START VERIFICATION ---\n";

try {
    $user = User::first();
    if (!$user) {
        // Create a user if none exists
        $user = User::factory()->create();
        echo "Created temporary user: {$user->id}\n";
    } else {
        echo "Using User: {$user->id}\n";
    }

    $scenario = ClinicalScenario::first();
    if (!$scenario) {
        echo "No scenario found. Creating one...\n";
        $scenario = ClinicalScenario::create([
            'title' => 'Test Scenario',
            'description' => 'Test',
            'type' => 'Test',
            'complexity' => 'Beginner',
            'objective' => [],
            'initial_patient_state' => [],
            'medical_history' => []
        ]);
    }
    echo "Using Scenario: {$scenario->id}\n";

    // Mock Request
    $request = Request::create('/api/v1/simulations/start', 'POST', [
        'scenario_id' => $scenario->id
    ]);

    // Mock Auth
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    // Call Controller
    $controller = app()->make(SimulationController::class);
    $response = $controller->start($request);

    echo "Status Code: " . $response->getStatusCode() . "\n";

    $content = json_decode($response->getContent(), true);
    if (isset($content['id'])) {
        echo "SUCCESS: Session Created with ID " . $content['id'] . "\n";
    } else {
        echo "FAILURE: " . $response->getContent() . "\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "--- END VERIFICATION ---\n";
