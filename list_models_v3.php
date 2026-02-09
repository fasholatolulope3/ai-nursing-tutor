<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.gemini.key');
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey";

$result = file_get_contents($url);
file_put_contents('all_models_v3.json', $result);
echo "Successfully wrote all_models_v3.json\n";
