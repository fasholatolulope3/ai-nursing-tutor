<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Generate a response from Gemini based on the conversation history.
     */
    public function generateResponse(array $history, array $currentContext)
    {
        // 1. Construct the prompt
        $contents = $this->formatHistory($history);

        // Add current patient context to the system instruction or latest message
        $systemInstruction = $this->buildSystemInstruction($currentContext);

        // 2. Call Gemini API
        // Note: For production, use a dedicated library or robust error handling.
        // We are using raw HTTP for transparency and control.

        // Check if API key is set
        if (empty($this->apiKey)) {
            return [
                'content' => "I see you haven't configured the Gemini API Key yet. I am running in simulation mode. Based on the protocols, we should monitor the patient's vitals closely.",
                'thought_signature' => "System: API Key missing. Returning fallback response.",
                'new_state' => null // No state change
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                        'contents' => $contents,
                        'system_instruction' => [
                            'parts' => [['text' => $systemInstruction]]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 1000,
                        ]
                    ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', $response->json());
                throw new \Exception('Failed to communicate with AI Tutor.');
            }

            $responseData = $response->json();
            $rawText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return $this->parseResponse($rawText);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'content' => "I apologize, but I'm having trouble connecting to my knowledge base right now. Please try again.",
                'thought_signature' => "Error: " . $e->getMessage(),
                'new_state' => null
            ];
        }
    }

    /**
     * Format conversation history for Gemini API.
     */
    protected function formatHistory(array $history)
    {
        return array_map(function ($turn) {
            return [
                'role' => $turn['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $turn['content']]]
            ];
        }, $history);
    }

    /**
     * Build the system instruction / persona.
     */
    protected function buildSystemInstruction(array $context)
    {
        return <<<EOT
You are an expert Clinical Nursing Tutor. Your goal is to guide a nursing student through a clinical simulation.
Current Patient Context:
- Condition: {$context['scenario_title']}
- Vitals: HR {$context['vitals']['hr']}, BP {$context['vitals']['bp']}, RR {$context['vitals']['rr']}, SaO2 {$context['vitals']['sao2']}

Instructions:
1. Act as the patient or a mentor depending on the situation.
2. Assess the student's actions.
3. If the student makes a critical error, intervene.
4. Provide a "Thought Signature" (your internal reasoning) wrapped in <thought> tags, followed by your response to the student.
5. If the patient's state changes based on the student's action, include a JSON block wrapped in <state> tags.
EOT;
    }

    /**
     * Parse the raw AI output into Thought, Content, and State.
     */
    protected function parseResponse(string $rawText)
    {
        // Extract Thought
        $thought = null;
        if (preg_match('/<thought>(.*?)<\/thought>/s', $rawText, $matches)) {
            $thought = trim($matches[1]);
            $rawText = str_replace($matches[0], '', $rawText);
        }

        // Extract State
        $newState = null;
        if (preg_match('/<state>(.*?)<\/state>/s', $rawText, $matches)) {
            try {
                $newState = json_decode($matches[1], true);
            } catch (\Exception $e) {
                Log::warning('Failed to parse state JSON');
            }
            $rawText = str_replace($matches[0], '', $rawText);
        }

        return [
            'content' => trim($rawText),
            'thought_signature' => $thought,
            'new_state' => $newState
        ];
    }
}
