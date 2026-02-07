<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Clinical Scenario Generation...\n";

try {
    $controller = $app->make(\App\Http\Controllers\Api\V1\ScenarioController::class);

    // Mock Request
    $request = \Illuminate\Http\Request::create(
        '/api/v1/scenarios/generate',
        'POST',
        [
            'type' => 'Clinical Simulation Command Center',
            'difficulty' => 'Intermediate',
            'role' => 'Charge Nurse',
            'thinking_level' => 'medium' // Optional, but good to test
        ]
    );

    $response = $controller->generate($request);

    echo "Status Code: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();

    // Pretty print JSON
    $data = json_decode($content, true);
    if ($data) {
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

        if (isset($data['status']) && $data['status'] === 'success') {
            echo "VERIFICATION PASSED: Scenario generated successfully.\n";
        } else {
            echo "VERIFICATION FAILED: Status is not success.\n";
        }
    } else {
        echo "Response: " . $content . "\n";
        echo "VERIFICATION FAILED: Invalid JSON response.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
