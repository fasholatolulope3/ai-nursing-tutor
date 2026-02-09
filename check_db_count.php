<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReferenceDocument;
use App\Models\SimulationSession;

$refs = ReferenceDocument::count();
$sims = SimulationSession::count();

file_put_contents('db_status.txt', "REFS: $refs\nSIMS: $sims\n");
echo "DONE\n";
