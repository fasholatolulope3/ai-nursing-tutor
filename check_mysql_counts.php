<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = [
    'ClinicalScenario' => \App\Models\ClinicalScenario::class,
    'SimulationSession' => \App\Models\SimulationSession::class,
    'ReferenceDocument' => \App\Models\ReferenceDocument::class,
    'ConversationTurn' => \App\Models\ConversationTurn::class,
    'ThoughtSignature' => \App\Models\ThoughtSignature::class,
];

echo "--- MySQL Record Counts ---\n";
foreach ($models as $name => $class) {
    try {
        $count = $class::count();
        echo "$name: $count\n";
    } catch (Exception $e) {
        echo "$name: Error (" . $e->getMessage() . ")\n";
    }
}
