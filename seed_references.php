<?php

use App\Models\ReferenceDocument;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- START SEEDING REFERENCES ---\n";

try {
    if (ReferenceDocument::count() === 0) {
        ReferenceDocument::create([
            'title' => 'ACL Protocol 2025',
            'file_path' => '/documents/acl_protocol_2025.pdf',
            'file_type' => 'pdf',
            'description' => 'Advanced Cardiac Life Support Protocol updates.'
        ]);

        ReferenceDocument::create([
            'title' => 'Pediatric Dosing Guide',
            'file_path' => '/documents/pediatric_dosing.pdf',
            'file_type' => 'pdf',
            'description' => 'Common pediatric medication dosages.'
        ]);

        ReferenceDocument::create([
            'title' => 'Sepsis Screening Tool',
            'file_path' => '/documents/sepsis_screen.pdf',
            'file_type' => 'pdf',
            'description' => 'Early detection of sepsis.'
        ]);

        echo "Seeded 3 reference documents.\n";
    } else {
        echo "References already exist.\n";
    }

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

echo "--- END SEEDING ---\n";
