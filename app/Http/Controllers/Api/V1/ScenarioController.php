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
        $validated = $request->validate([
            'type' => 'required|string',
            'difficulty' => 'required|string|in:Beginner,Intermediate,Advanced',
            'role' => 'required|string',
        ]);

        try {
            $scenarioData = $this->geminiService->generateScenario(
                $validated['type'],
                $validated['difficulty'],
                $validated['role']
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
        return response()->json([]);
    }

    /**
     * Show scenario (Placeholder)
     */
    public function show($id)
    {
        return response()->json([]);
    }
}
