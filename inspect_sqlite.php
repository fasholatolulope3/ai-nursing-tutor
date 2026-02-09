<?php
$databases = ['nursing_db', 'database/database.sqlite'];
$logFile = 'sqlite_inspection.log';
$output = "";

foreach ($databases as $dbFile) {
    if (!file_exists($dbFile)) {
        $output .= "File $dbFile does not exist.\n";
        continue;
    }
    $output .= "--- Inspecting $dbFile ---\n";
    try {
        $db = new SQLite3($dbFile);
        $res = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $tableName = $row['name'];
            if (in_array($tableName, ['sqlite_sequence']))
                continue;
            try {
                $count = $db->querySingle("SELECT count(*) FROM \"$tableName\"");
                $output .= "Table: [" . str_pad($tableName, 25) . "] | Count: $count\n";
            } catch (Exception $e) {
                $output .= "Table: [" . str_pad($tableName, 25) . "] | Error: " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        $output .= "Error: " . $e->getMessage() . "\n";
    }
    $output .= "\n";
}

file_put_contents($logFile, $output);
echo "Inspection complete. See $logFile\n";
