<?php

use App\Models\ReferenceDocument;
use App\Models\SimulationSession;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DEBUG SIDEBAR DATA ---\n";

try {
    $refs = ReferenceDocument::all();
    echo "ReferenceDocument Count: " . $refs->count() . "\n";
    if ($refs->count() > 0) {
        echo "First Reference: " . $refs->first()->title . "\n";
    }

    $sims = SimulationSession::count();
    echo "SimulationSession Count (Total): " . $sims . "\n";

    // Check specific user if we knew the ID, but global count helps.

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "--- END DEBUG ---\n";
