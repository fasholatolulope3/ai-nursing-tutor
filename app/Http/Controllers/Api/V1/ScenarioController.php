<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScenarioController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generate a new clinical scenario.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        ini_set('max_execution_time', 120);
        set_time_limit(120); // Allow up to 2 minutes for AI generation

        $validated = $request->validate([
            'type' => 'required|string',
            'difficulty' => 'required|string|in:Beginner,Intermediate,Advanced',
            'role' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            $scenarioData = $this->geminiService->generateScenario(
                $validated['type'],
                $validated['difficulty'],
                $validated['role'],
                $validated['description'] ?? null
            );

            // Map Gemini Response to Database Model
            $scenario = \App\Models\ClinicalScenario::create([
                'title' => $scenarioData['scenario_briefing']['title'] ?? 'Generated Scenario',
                'description' => $scenarioData['scenario_briefing']['story'] ?? 'No description provided.',
                'objective' => $scenarioData['learning_objectives'] ?? [],
                'complexity' => strtolower($scenarioData['difficulty'] ?? 'beginner'),
                'initial_patient_state' => array_merge(
                    $scenarioData['initial_vitals'] ?? [],
                    ['profile' => $scenarioData['scenario_briefing']['patient_profile'] ?? []]
                ),
                'medical_history' => [], // Gemini doesn't explicitly return this in current schema, could be added later
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $scenario
            ]);
        } catch (\Exception $e) {
            Log::error('Scenario Generation Failed: ' . $e->getMessage());

            $errorMessage = 'Failed to generate scenario. Please try again.';
            $statusCode = 500;

            if (str_contains($e->getMessage(), '429') || str_contains(strtolower($e->getMessage()), 'quota')) {
                $errorMessage = 'AI Service Busy (Quota Exceeded). Please wait a minute and try again.';
                $statusCode = 429;
            } elseif (str_contains($e->getMessage(), '503') || str_contains(strtolower($e->getMessage()), 'overloaded') || str_contains(strtolower($e->getMessage()), 'unavailable')) {
                $errorMessage = 'AI Engine Overloaded. The clinical factory is currently at capacity. Please try again in a few seconds.';
                $statusCode = 503;
            }

            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], $statusCode);
        }
    }

    /**
     * List scenarios (Placeholder for resource controller compatibility if needed)
     */
    public function index()
    {
        return response()->json(\App\Models\ClinicalScenario::latest()->paginate(10));
    }

    /**
     * Show scenario (Placeholder)
     */
    public function show($id)
    {
        return response()->json([]);
    }
}
