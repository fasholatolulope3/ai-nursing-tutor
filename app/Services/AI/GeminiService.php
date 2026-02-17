<?php

namespace App\Services\AI;

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

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        // 1. Construct System Instruction
        $systemInstruction = "You are the 'Clinical Context' AI Nursing Tutor.
        
Core Operational Directives:
1. Process all inputs with high-level clinical reasoning.
2. If an image (chart/lab) is provided, extract vital data.
3. Follow the nursing process.
4. Update the thoughtSignature to track state.

Output strictly in JSON format:
{
    \"answer\": \"Detailed clinical response...\",
    \"reasoning_trace\": [
        {\"step\": 1, \"content\": \"Identifying primary symptom\", \"status\": \"completed\"},
        {\"step\": 2, \"content\": \"Analyzing vitals\", \"status\": \"completed\"}
    ],
    \"extracted_data\": {\"BP\": \"120/80\", ...},
    \"new_signature\": \"Updated summary of clinical state...\"
}";

        // 2. Build Payload
        $parts = [];

        $parts[] = ['text' => $message];

        if ($attachment) {
            $mimeType = $attachment->getMimeType();
            $data = base64_encode(file_get_contents($attachment->getPathname()));

            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $data
                ]
            ];
        }

        if ($previousSignature) {
            $parts[] = ['text' => "\n[Context - Previous System State]: " . $previousSignature];
        }

        $payload = [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ],
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'responseMimeType' => 'application/json'
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)
                ->connectTimeout(10)
                ->retry(3, 2000)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error (Clinical Query)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini API request failed: ' . $response->body());
            }

            $data = $response->json();

            // Log raw response for debugging
            Log::info('Gemini Raw Response', ['candidates' => $data['candidates'] ?? 'no_candidates']);

            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            // Cleanup markdown code blocks if present
            $cleanText = preg_replace('/^```json\s*|\s*```$/', '', $rawText);

            $parsed = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini JSON Parse Error', ['raw_text' => $rawText, 'clean_text' => $cleanText, 'json_error' => json_last_error_msg()]);
                return [
                    'answer' => $cleanText, // Fallback to raw text if JSON fails
                    'reasoning_trace' => [],
                    'extracted_data' => [],
                    'new_signature' => $previousSignature,
                    'related_topics' => []
                ];
            }

            // Handle if model returned a list/array instead of an object
            if (array_is_list($parsed) && !empty($parsed)) {
                Log::warning('Gemini returned a JSON list instead of an object', ['parsed' => $parsed]);
                $firstItem = $parsed[0];

                if (is_array($firstItem) && isset($firstItem['answer'])) {
                    $parsed = $firstItem;
                } elseif (is_string($firstItem)) {
                    // If it's a list of strings, join them or take the first as answer
                    $parsed = [
                        'answer' => implode("\n", $parsed),
                        'reasoning_trace' => [],
                        'extracted_data' => [],
                        'new_signature' => $previousSignature,
                        'related_topics' => []
                    ];
                } else {
                    // Fallback for unknown array structure
                    $parsed = [
                        'answer' => json_encode($parsed),
                        'reasoning_trace' => [],
                        'extracted_data' => [],
                        'new_signature' => $previousSignature,
                        'related_topics' => []
                    ];
                }
            }

            // Ensure 'answer' key exists
            if (!isset($parsed['answer']) || empty($parsed['answer'])) {
                Log::warning('Gemini Response Missing Answer Key', ['parsed' => $parsed]);
                $parsed['answer'] = "I analyzed the document but couldn't generate a specific clinical answer. Please check the document content.";
            }

            return $parsed;

        } catch (Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
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

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $systemInstruction = <<<EOT
You are Gemini, accessed via the Google Gemini API (v1beta).

You are operating as a Clinical Scenario Factory for a nursing education
platform built with Laravel (backend) and Vue 3 (frontend).

This prompt will be sent directly to:
You must follow Gemini API rules strictly.

====================================
MODE OF OPERATION
====================================
- Output JSON only
- No markdown
- No explanations
- No chain-of-thought
- No conversational text

You must adapt reasoning depth using the Gemini thinking_level parameter
provided in generationConfig.

====================================
OBJECTIVE
====================================
Generate a professional, agentic “Clinical Simulation Command Center”
scenario used as a prebriefing page for high-stakes nursing simulations.

The output will populate:
- Scenario briefing
- Learning objectives
- Readiness indicators
- Initial vitals
- Agentic vision preview
- Case metadata

====================================
INPUT VARIABLES
====================================
You will receive the following values at runtime:

- scenario_type
- difficulty_level (Beginner | Intermediate | Advanced)
- learner_role
- reference_documents (array)
- thinking_level (low | medium | high)

You MUST scale complexity, ambiguity, and clinical depth accordingly.

====================================
OUTPUT SCHEMA (STRICT)
====================================
Return a single JSON object:

{
  "scenario_briefing": {
    "title": "",
    "patient_profile": {
      "age": "",
      "gender": "",
      "chief_complaint": "",
      "clinical_setting": ""
    },
    "story": ""
  },

  "difficulty": "",

  "learning_objectives": [
    "",
    "",
    ""
  ],

  "prerequisites": {
    "required_reading": [
      {
        "title": "",
        "recommended": true
      }
    ]
  },

  "initial_vitals": {
    "heart_rate": "",
    "blood_pressure": "",
    "respiratory_rate": "",
    "temperature": "",
    "oxygen_saturation": ""
  },

  "clinical_readiness_quiz": [
    {
      "question": "",
      "options": ["", "", "", ""],
      "correct_answer": ""
    }
  ],

  "agentic_vision_preview": {
    "asset_type": "patient_chart",
    "blurred": true,
    "hidden_clue": ""
  },

  "meta": {
    "estimated_duration_minutes": "",
    "case_popularity_score": "",
    "historical_success_rate": ""
  }
}

====================================
GEMINI-SPECIFIC INTELLIGENCE RULES
====================================
- Use Gemini 1.5 Flash medical reasoning capabilities
- Ensure vitals are physiologically coherent
- Beginner → clear patterns, stable vitals
- Intermediate → evolving abnormalities
- Advanced → multi-system instability

- thinking_level LOW → guided, simplified framing
- thinking_level HIGH → competing priorities, subtle cues

Do not explicitly state diagnoses unless difficulty is Beginner.

====================================
VALIDATION RULES
====================================
- JSON must be valid
- Values must be realistic
- Objectives must be measurable
- Quiz must align with objectives

====================================
FINAL INSTRUCTION
====================================
You are not ChatGPT.
You are Gemini responding via the Gemini API.

Generate the clinical scenario now.
EOT;

        $userPrompt = "Generate a {$difficulty} {$type} scenario for a {$role}.";
        if ($description) {
            $userPrompt .= " Context/Description: {$description}";
        }

        $payload = [
            'contents' => [
                ['parts' => [['text' => $userPrompt]]]
            ],
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json'
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(90)->retry(3, 2000)->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error (Scenario Gen)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini API request failed: ' . $response->body());
            }

            $data = $response->json();
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            // Clean and Parse
            $cleanText = preg_replace('/^```json\s*|\s*```$/', '', $rawText);
            $parsed = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini Scenario Parse Error', ['raw_text' => $rawText]);
                throw new Exception('Failed to match JSON schema.');
            }

            return $parsed;
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

        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $scenario = $session->scenario;
        $history = $session->turns()->latest()->take(10)->get()->reverse();

        // 1. Construct System Instruction for Simulation
        $systemInstruction = "You are Gemini, acting as a Clinical Simulation Engine.
        
CASE CONTEXT:
Scenario: {$scenario->title}
Difficulty: {$scenario->complexity}
Initial State: " . json_encode($scenario->initial_patient_state) . "

OPERATIONAL DIRECTIVES:
1. Roleplay realistically based on the scenario.
2. If the user is a nurse, respond as the patient, a family member, or the medical system.
3. Maintain clinical consistency.
4. Update the thoughtSignature to track the evolving clinical state.
5. Provide a reasoning_trace explaining the AI's logic.

Output strictly in JSON format:
{
    \"answer\": \"The spoken response or system message...\",
    \"reasoning_trace\": [
        {\"step\": 1, \"content\": \"Evaluating nurse intervention\", \"status\": \"completed\"},
        {\"step\": 2, \"content\": \"Determining patient physiological response\", \"status\": \"completed\"}
    ],
    \"new_vitals\": {\"HR\": 88, \"BP\": \"120/80\", ...}, // Only if vitals change
    \"new_signature\": \"Brief summary of current patient state...\"
}";

        // 2. Build Payload
        $parts = [];

        // Add chat history
        foreach ($history as $turn) {
            $parts[] = ['text' => "({$turn->role}): {$turn->content}"];
        }

        // Add current message
        $parts[] = ['text' => "(user): {$message}"];

        $payload = [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ],
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json'
            ]
        ];

        try {
            Log::info('Gemini Simulation Request Payload', ['url' => $url, 'payload' => $payload]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->retry(3, 2000)->post($url, $payload);

            Log::info('Gemini Simulation Raw Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error (Simulation Turn)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini API request failed.');
            }

            $data = $response->json();
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $cleanText = preg_replace('/^```json\s*|\s*```$/', '', $rawText);
            $parsed = json_decode($cleanText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini Simulation Turn Parse Error', ['raw_text' => $rawText]);
                return [
                    'answer' => $cleanText,
                    'reasoning_trace' => [],
                    'new_signature' => 'Error parsing state'
                ];
            }

            return $parsed;

        } catch (Exception $e) {
            Log::error('Gemini Service Exception (Simulation): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
