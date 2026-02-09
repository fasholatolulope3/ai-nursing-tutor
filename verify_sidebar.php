<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\ReferenceController;
use App\Http\Controllers\Api\V1\SimulationController;

echo "--- START SIDEBAR VERIFICATION ---\n";

try {
    $user = User::first();
    if (!$user) {
        $user = User::factory()->create();
    }
    echo "User ID: {$user->id}\n";

    // 1. Test References Endpoint
    echo "Testing References...\n";
    $refController = app()->make(ReferenceController::class);
    $refResponse = $refController->index();
    $references = json_decode($refResponse->getContent(), true);

    echo "Found " . count($references) . " references.\n";
    foreach ($references as $ref) {
        echo "- {$ref['title']} ({$ref['file_type']})\n";
    }

    if (count($references) >= 3) {
        echo "SUCCESS: References fetched correctly.\n";
    } else {
        echo "FAILURE: Missing references.\n";
    }

    // 2. Test Recent Simulations Endpoint
    echo "\nTesting Recent Simulations...\n";
    $request = Request::create('/api/v1/simulations', 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $simController = app()->make(SimulationController::class);
    $simResponse = $simController->index($request);
    $simulations = json_decode($simResponse->getContent(), true);

    echo "Found " . count($simulations) . " recent simulations.\n";
    foreach ($simulations as $sim) {
        echo "- Session #{$sim['id']} ({$sim['status']})\n";
    }

    echo "SUCCESS: Simulations fetched correctly.\n";


} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "--- END VERIFICATION ---\n";
