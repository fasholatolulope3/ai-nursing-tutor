<?php
$logFile = 'storage/logs/laravel.log';
$lines = file($logFile);
$lastErrorIndex = -1;

// Find the last line starting with "local.ERROR" (case insensitive search not needed for standard logs but good practice)
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (strpos($lines[$i], 'local.ERROR') !== false) {
        $lastErrorIndex = $i;
        break;
    }
}

if ($lastErrorIndex !== -1) {
    echo "Found error at line " . ($lastErrorIndex + 1) . ":\n";
    // Print 20 lines starting from the error
    for ($j = $lastErrorIndex; $j < min($lastErrorIndex + 20, count($lines)); $j++) {
        echo $lines[$j];
    }
} else {
    echo "No errors found.";
}
