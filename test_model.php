<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Attempting to create a ClinicalScenario instance...\n";
    $scenario = new \App\Models\ClinicalScenario();
    echo "Instance created.\n";

    // Check if table exists (optional, but good)
    // echo "Table: " . $scenario->getTable() . "\n";

    echo "Attempting to create a record...\n";
    $record = \App\Models\ClinicalScenario::create([
        'title' => 'Test Scenario',
        'description' => 'Test Description',
        'complexity' => 'beginner',
        'objective' => ['Test Objective'],
        'initial_patient_state' => []
    ]);
    echo "Record created with ID: " . $record->id . "\n";

    // Clean up
    $record->forceDelete();
    echo "Record deleted.\n";

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
