<?php

use App\Models\ClinicalScenario;
use App\Models\ReferenceDocument;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- START SEEDING TAGS ---\n";

try {
    // Seed Scenarios
    $scenarios = ClinicalScenario::all();
    foreach ($scenarios as $scenario) {
        $tags = [];
        if (stripos($scenario->title, 'sepsis') !== false)
            $tags[] = 'sepsis';
        if (stripos($scenario->title, 'cardiac') !== false)
            $tags[] = 'cardiac';
        if (stripos($scenario->title, 'pediatric') !== false)
            $tags[] = 'pediatric';
        if (empty($tags))
            $tags[] = 'general';

        $scenario->update(['tags' => $tags]);
        echo "Updated Scenario '{$scenario->title}' with tags: " . implode(', ', $tags) . "\n";
    }

    // Seed References
    $refs = ReferenceDocument::all();
    foreach ($refs as $ref) {
        $tags = [];
        if (stripos($ref->title, 'ACL') !== false)
            $tags[] = 'cardiac';
        if (stripos($ref->title, 'Pediatric') !== false)
            $tags[] = 'pediatric';
        if (stripos($ref->title, 'Sepsis') !== false)
            $tags[] = 'sepsis';

        $ref->update(['tags' => $tags]);
        echo "Updated Reference '{$ref->title}' with tags: " . implode(', ', $tags) . "\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "--- END SEEDING TAGS ---\n";
