<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model = 'gemini-1.5-pro';

    public function __construct()
    {
        // Point to the local Node.js proxy server
        $this->baseUrl = config('services.gemini.proxy_url', 'http://localhost:3001');
        $this->apiKey = config('services.gemini.key');

        if (empty($this->apiKey)) {
            Log::error('Gemini API key is missing in AI\GeminiService.');
        }
    }

    /**
     * Process a clinical query with the Gemini API.
     * 
     * @param string $message
     * @param \Illuminate\Http\UploadedFile|null $attachment
     * @param string|null $previousSignature
     * @return array
     */
    public function handleClinicalQuery(string $message, $attachment = null, ?string $previousSignature = null): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }

        $url = "{$this->baseUrl}/analyze";

        $payload = [
            'message' => $message,
            'previousSignature' => $previousSignature,
            'attachment' => null
        ];

        if ($attachment) {
            $payload['attachment'] = [
                'data' => base64_encode(file_get_contents($attachment->getPathname())),
                'mimeType' => $attachment->getMimeType()
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini Proxy Error (Clinical Query)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini Proxy request failed: ' . $response->body());
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Gemini Proxy Exception: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Generate a clinical scenario based on parameters.
     * 
     * @param string $type
     * @param string $difficulty
     * @param string $role
     * @return array
     */
    public function generateScenario(string $type, string $difficulty, string $role, ?string $description = null): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }

        $url = "{$this->baseUrl}/generate-scenario";

        $payload = [
            'type' => $type,
            'difficulty' => $difficulty,
            'role' => $role,
            'description' => $description
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(90)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini Proxy Error (Scenario Gen)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini Proxy request failed: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            // Check for Quota Exceeded (429) OR Model Not Found (404) and use Fallback
            if (
                str_contains($e->getMessage(), '429') ||
                str_contains($e->getMessage(), '404') ||
                str_contains(strtolower($e->getMessage()), 'quota')
            ) {
                Log::warning('Gemini API Error. Using Fallback Data for Scenario.', ['error' => $e->getMessage()]);
                $fallbacks = FallbackData::getScenarios();
                return $fallbacks[array_rand($fallbacks)];
            }

            Log::error('Gemini Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Handle an interactive simulation turn.
     * 
     * @param \App\Models\SimulationSession $session
     * @param string $message
     * @return array
     */
    public function handleSimulationTurn(\App\Models\SimulationSession $session, string $message): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }

        $url = "{$this->baseUrl}/simulation-turn";

        $scenario = $session->scenario;
        $history = $session->turns()->latest()->take(10)->get()->reverse();

        $payload = [
            'scenarioTitle' => $scenario->title,
            'complexity' => $scenario->complexity,
            'initialPatientState' => $scenario->initial_patient_state,
            'history' => $history->map(fn($t) => ['role' => $t->role, 'content' => $t->content])->toArray(),
            'message' => $message
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $payload);

            Log::info('Gemini Proxy Simulation Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                Log::error('Gemini Proxy Error (Simulation Turn)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini Proxy request failed.');
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Gemini Service Exception (Simulation): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
