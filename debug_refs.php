<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReferenceDocument;

$refs = ReferenceDocument::all();
foreach ($refs as $ref) {
    echo "ID: {$ref->id} | Title: {$ref->title} | Path: {$ref->file_path}\n";
}
