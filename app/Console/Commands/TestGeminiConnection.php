<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestGeminiConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:gemini-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Gemini API connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('--- Starting Gemini Connection Debug (Test Gemini 3) ---');
        $this->info('Starting Gemini Connection Debug...');

        $config = config('services.gemini');
        $apiKey = $config['key'] ?? null;
        $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

        if (!$apiKey) {
            $this->error('API Key is missing!');
            return;
        }

        $model = 'gemini-2.0-flash-exp';
        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $safeUrl = preg_replace('/key=([^&]+)/', 'key=REDACTED', $url);
        $this->info("Testing URL: " . $safeUrl);
        Log::info("Testing URL: " . $safeUrl);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                        'contents' => [
                            ['parts' => [['text' => 'Hello, confirm you are Gemini 3?']]]
                        ]
                    ]);

            $this->info("Status Code: " . $response->status());
            Log::info("Status Code: " . $response->status());

            if ($response->successful()) {
                $this->info("Response Success!");
                Log::info("Response Success!");
                Log::info(substr($response->body(), 0, 1000));
                $this->info("Content: " . substr($response->body(), 0, 200));
            } else {
                $this->error("Response Failed!");
                Log::error("Response Failed!");
                Log::error($response->body());
            }

        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            Log::error("Exception: " . $e->getMessage());
        }
    }
}
