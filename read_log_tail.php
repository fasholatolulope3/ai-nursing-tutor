<?php
$logFile = 'storage/logs/laravel.log';
$content = file_get_contents($logFile);
// Get last 2000 chars
echo substr($content, -2000);
