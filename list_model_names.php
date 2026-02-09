<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.gemini.key');
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (strpos($model['name'], 'flash') !== false || strpos($model['name'], 'pro') !== false) {
            echo $model['name'] . "\n";
        }
    }
}
