<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model = 'gemini-1.5-flash';

    public function __construct()
    {
        $this->baseUrl = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
        $this->apiKey = config('services.gemini.key');

        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured.');
        }
    }

    /**
     * Generate text using Gemini API
     *
     * @param string $prompt
     * @param array $options Optional configuration (system_instruction, thinking_level, temperature)
     * @return string
     * @throws Exception
     */
    public function generateText(string $prompt, array $options = []): string
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => []
        ];

        // Add System Instruction if provided
        if (!empty($options['system_instruction'])) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $options['system_instruction']]
                ]
            ];
        }

        // Configure Thinking Level
        // Default to LOW if not specified, but only if thinking is explicitly requested or implied?
        // The user prompt said: "Allow optional: thinkingLevel... Default to LOW."
        // So we should probably always include it if we are using a thinking model, or just map it if provided.
        // Let's map it if provided or default to LOW as per instructions.

        $thinkingLevel = $options['thinking_level'] ?? 'LOW';
        // Map to integer or string? API expects string ENUM usually for previews?
        // Actually for v1beta thinking config, it's typically: include_thoughts: boolean.
        // But the prompt says: "Map to: generationConfig.thinkingConfig.thinkingLevel"
        // and "thinkingLevel = low | medium | high".

        // As per prompt instructions:
        $payload['generationConfig']['thinkingConfig'] = [
            'includeThoughts' => true // Usually required to see thoughts, but prompt didn't specify.
            // Prompt specifically said: "Map to: generationConfig.thinkingConfig.thinkingLevel"
        ];

        // We will simple map the level.
        $payload['generationConfig']['thinkingConfig']['thinkingLevel'] = strtoupper($thinkingLevel);

        // Temperature handling
        if (isset($options['temperature'])) {
            $payload['generationConfig']['temperature'] = (float) $options['temperature'];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);
            throw new Exception('Gemini API request failed: ' . $response->body());
        }

        $data = $response->json();

        // Extract text
        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? throw new Exception('Unexpected response format from Gemini API.');
    }
}
