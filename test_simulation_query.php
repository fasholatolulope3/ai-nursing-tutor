<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a dummy user or find one
$user = \App\Models\User::first();
if (!$user) {
    echo "Creating temp user...\n";
    $user = \App\Models\User::forceCreate([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
}

echo "Testing Clinical Query Endpoint as User: {$user->id}\n";

$content = json_encode([
    'message' => 'The patient has elevated HR and low BP. What is the priority?',
    'previous_thought_signature' => null
]);

$request = Illuminate\Http\Request::create(
    '/api/v1/simulations/clinical-query',
    'POST',
    [],
    [],
    [],
    [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json'
    ],
    $content
);

// Authenticate globally for any helpers
auth()->login($user);
$request->setUserResolver(fn() => $user);

// Instantiate Controller directly
try {
    $controller = $app->make(\App\Http\Controllers\Api\V1\SimulationController::class);
    $response = $controller->handleClinicalQuery($request);

    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";

    if ($response->getStatusCode() === 200) {
        // Check for JSON structure
        $json = json_decode($response->getContent(), true);
        if (isset($json['answer']) && isset($json['reasoning_trace'])) {
            echo "VERIFICATION PASSED\n";
            exit(0);
        } else {
            echo "JSON Structure Mismatch\n";
            print_r($json);
            exit(1);
        }
    } else {
        echo "VERIFICATION FAILED\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
