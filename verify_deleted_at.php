<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SimulationSession;
use Illuminate\Support\Facades\Schema;

echo "--- START DELETED_AT VERIFICATION ---\n";

try {
    // 1. Check Schema
    $hasColumn = Schema::hasColumn('simulation_sessions', 'deleted_at');
    echo "Column 'deleted_at' exists: " . ($hasColumn ? 'YES' : 'NO') . "\n";

    if (!$hasColumn) {
        throw new Exception("Column missing!");
    }

    // 2. Try Query (This triggered the original error)
    $count = SimulationSession::count();
    echo "SimulationSession query successful. Count: $count\n";

    // 3. Try Soft Delete
    $session = SimulationSession::first();
    if ($session) {
        echo "Found session ID: {$session->id}. Testing soft delete...\n";
        $session->delete();
        echo "Soft deleted successfully.\n";

        $check = SimulationSession::find($session->id);
        echo "Find (without trashed): " . ($check ? 'Found' : 'Not Found (Correct)') . "\n";

        $checkTrashed = SimulationSession::withTrashed()->find($session->id);
        echo "Find (with trashed): " . ($checkTrashed ? 'Found (Correct)' : 'Not Found') . "\n";

        // Restore
        $session->restore();
        echo "Restored successfully.\n";
    } else {
        echo "No sessions to test soft delete on.\n";
    }

    echo "SUCCESS: Verification passed.\n";

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "--- END VERIFICATION ---\n";
