<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReferenceDocument;

// Remove old/duplicate records based on title or path
ReferenceDocument::whereIn('title', ['ACL Protocol 2025', 'Pediatric Dosing Guide', 'Sepsis Screening Tool'])->delete();

echo "Duplicate cleanup DONE\n";
