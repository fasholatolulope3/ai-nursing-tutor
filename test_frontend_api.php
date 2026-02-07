<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Frontend API Endpoint...\n";

// Simulate the Request
$content = json_encode([
    'prompt' => 'Frontend verification test',
    'thinking_level' => 'LOW'
]);

// Manually construct request with proper content type handling for JSON
$request = Illuminate\Http\Request::create(
    '/api/v1/gemini',
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

// Dispatch
$response = $app->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";

if ($response->getStatusCode() === 200 && str_contains($response->getContent(), '"status":"success"')) {
    echo "VERIFICATION PASSED\n";
    exit(0);
} else {
    echo "VERIFICATION FAILED\n";
    exit(1);
}
