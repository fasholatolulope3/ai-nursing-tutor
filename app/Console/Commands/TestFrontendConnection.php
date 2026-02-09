<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SimulationSession;
use Illuminate\Support\Facades\Log;

class TestFrontendConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:frontend-connection {sessionId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a frontend chat request';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Frontend Connection Simulation...');

        $user = User::first();
        if (!$user) {
            $this->error('No users found.');
            return;
        }
        $this->info("Acting as user: {$user->email} (ID: {$user->id})");

        // Find a session
        $sessionId = $this->argument('sessionId');
        if (!$sessionId) {
            $session = SimulationSession::where('user_id', $user->id)->latest()->first();
            if ($session) {
                $sessionId = $session->id;
                $this->info("Found latest session: {$sessionId}");
            } else {
                $this->info("Creating new simulation session...");
                $scenario = \App\Models\ClinicalScenario::first();
                if (!$scenario) {
                    $this->error("No scenarios found to create session.");
                    return;
                }
                $session = SimulationSession::create([
                    'user_id' => $user->id,
                    'scenario_id' => $scenario->id,
                    'status' => 'active',
                    'started_at' => now(),
                ]);
                $sessionId = $session->id;
                $this->info("Created session: {$sessionId}");
            }
        }

        $this->info("Testing Session ID: {$sessionId}");

        // Prepare Request
        $uri = "/api/v1/simulations/{$sessionId}/chat";
        $data = ['message' => 'Test message from console debug'];

        $request = \Illuminate\Http\Request::create($uri, 'POST', $data);
        $request->headers->set('Accept', 'application/json');

        // Mock Auth
        if (class_exists(\Laravel\Sanctum\Sanctum::class)) {
            \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        }

        // Dispatch
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);

        $this->info("Status: " . $response->status());

        if ($response->isSuccessful()) {
            $this->info("Response:");
            $content = $response->getContent();
            $decoded = json_decode($content, true);
            $this->line(json_encode($decoded, JSON_PRETTY_PRINT));
        } else {
            $this->error("Failed!");
            $this->error($response->getContent());
            if ($response->exception) {
                $this->error("Exception: " . $response->exception->getMessage());
                // $this->error($response->exception->getTraceAsString());
            }
        }
    }
}
