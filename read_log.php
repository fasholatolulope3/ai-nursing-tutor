<?php
$logFile = 'storage/logs/laravel.log';
$lines = file($logFile);
$found = null;
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (strpos($lines[$i], 'Gemini Raw Response') !== false) {
        $found = $lines[$i];
        break;
    }
}
if ($found) {
    echo $found;
} else {
    echo "Log entry not found.";
}
