<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\AiInteraction;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\SimulationController;

echo "--- START NAVIGATION VERIFICATION ---\n";

try {
    $user = User::first();
    if (!$user) {
        $user = User::factory()->create();
    }
    echo "User ID: {$user->id}\n";

    // 1. Create a dummy interaction
    $interaction = AiInteraction::create([
        'user_id' => $user->id,
        'prompt' => 'Navigation Test',
        'ai_response' => ['answer' => 'Test Answer']
    ]);
    echo "Created interaction ID: {$interaction->id}\n";

    // 2. Test GET specific interaction endpoint via Route (mock request)
    // We can't easily test route dispatch in raw script without booting full HTTP stack, 
    // but we can test the Controller method directly if we mock the request properly.
    // Ideally we should assume the Route definition works if the method works.

    $request = Request::create("/api/v1/simulations/clinical-query/{$interaction->id}", 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $controller = app()->make(SimulationController::class);
    $response = $controller->getInteraction($request, (string) $interaction->id);

    echo "Status Code: " . $response->getStatusCode() . "\n";
    $content = json_decode($response->getContent(), true);

    if (isset($content['id']) && $content['id'] == $interaction->id) {
        echo "SUCCESS: Retrieved specific interaction.\n";
    } else {
        echo "FAILURE: Content mismatch.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "--- END VERIFICATION ---\n";
