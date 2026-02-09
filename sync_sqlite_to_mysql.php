<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ClinicalScenario;
use App\Models\SimulationSession;
use App\Models\ConversationTurn;
use App\Models\AiInteraction;
use App\Models\ReferenceDocument;

$databases = ['nursing_db', 'database/database.sqlite'];

echo "--- START SYNCING SQLITE TO MYSQL ---\n";

function tableExists($sqlite, $tableName)
{
    $res = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$tableName'");
    return $res->fetchArray() !== false;
}

foreach ($databases as $dbFile) {
    if (!file_exists($dbFile)) {
        echo "Skipping $dbFile (not found)\n";
        continue;
    }
    echo "Processing $dbFile...\n";
    $sqlite = new SQLite3($dbFile);

    // 1. Sync Scenarios
    if (tableExists($sqlite, 'clinical_scenarios')) {
        $res = $sqlite->query("SELECT * FROM clinical_scenarios");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $exists = ClinicalScenario::where('title', $row['title'])->first();
            if (!$exists) {
                ClinicalScenario::create([
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'objective' => json_decode($row['objective'], true),
                    'complexity' => $row['complexity'],
                    'initial_patient_state' => json_decode($row['initial_patient_state'], true),
                    'medical_history' => json_decode($row['medical_history'] ?? '[]', true),
                    'tags' => json_decode($row['tags'] ?? '[]', true),
                ]);
                echo "Migrated Scenario: {$row['title']}\n";
            }
        }
    }

    // 2. Sync Reference Documents
    if (tableExists($sqlite, 'reference_documents')) {
        $res = $sqlite->query("SELECT * FROM reference_documents");
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $exists = ReferenceDocument::where('title', $row['title'])->first();
            if (!$exists) {
                ReferenceDocument::create([
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'file_path' => $row['file_path'],
                    'file_type' => $row['file_type'] ?? 'pdf',
                    'tags' => json_decode($row['tags'] ?? '[]', true),
                ]);
                echo "Migrated Reference: {$row['title']}\n";
            }
        }
    }

    // 3. Sync AI Interactions
    if (tableExists($sqlite, 'ai_interactions')) {
        $user = User::first();
        if ($user) {
            $data = $sqlite->query("SELECT * FROM ai_interactions");
            while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
                AiInteraction::create([
                    'user_id' => $user->id,
                    'prompt' => $row['prompt'],
                    'ai_response' => json_decode($row['ai_response'], true),
                    'created_at' => $row['created_at'],
                ]);
            }
            echo "Migrated AI Interactions.\n";
        }
    }

    // 4. Sync Simulation Sessions & Turns
    if (tableExists($sqlite, 'simulation_sessions')) {
        $user = User::first();
        $sessionsData = $sqlite->query("SELECT * FROM simulation_sessions");
        while ($sessionRow = $sessionsData->fetchArray(SQLITE3_ASSOC)) {
            // Check if scenario exists in MySQL
            $origScenarioTitle = $sqlite->querySingle("SELECT title FROM clinical_scenarios WHERE id = " . $sessionRow['scenario_id']);
            $scenario = ClinicalScenario::where('title', $origScenarioTitle)->first();

            if ($user && $scenario) {
                // Check if session already exists (by started_at/scenario_id approx)
                $startedAt = $sessionRow['started_at'] ?? $sessionRow['created_at'];
                $exists = SimulationSession::where('user_id', $user->id)
                    ->where('scenario_id', $scenario->id)
                    ->where('started_at', $startedAt)
                    ->first();

                if (!$exists) {
                    $newSession = SimulationSession::create([
                        'user_id' => $user->id,
                        'scenario_id' => $scenario->id,
                        'status' => $sessionRow['status'],
                        'score' => $sessionRow['score'] ?? null,
                        'feedback_summary' => json_decode($sessionRow['feedback_summary'] ?? '[]', true),
                        'started_at' => $startedAt,
                        'completed_at' => $sessionRow['completed_at'] ?? null,
                    ]);

                    // Sync turns for this session
                    if (tableExists($sqlite, 'conversation_turns')) {
                        $turnsData = $sqlite->query("SELECT * FROM conversation_turns WHERE simulation_session_id = " . $sessionRow['id']);
                        while ($turnRow = $turnsData->fetchArray(SQLITE3_ASSOC)) {
                            ConversationTurn::create([
                                'simulation_session_id' => $newSession->id,
                                'role' => $turnRow['role'],
                                'content' => $turnRow['content'],
                                'created_at' => $turnRow['created_at'],
                            ]);
                        }
                    }
                }
            }
        }
        echo "Migrated Simulation Sessions and Turns.\n";
    }
}

echo "--- SYNC COMPLETE ---\n";
