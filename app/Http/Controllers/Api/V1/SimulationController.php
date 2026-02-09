<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $geminiService;

    public function __construct(\App\Services\AI\GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->simulationSessions()
                ->with('scenario')
                ->latest()
                ->take(5) // Limit for sidebar
                ->get()
        );
    }

    /**
     * Start a new simulation session.
     */
    public function start(Request $request)
    {
        set_time_limit(60);

        try {
            \Illuminate\Support\Facades\Log::info('Explicit Start Simulation Request', ['user_id' => $request->user()->id]);

            $validated = $request->validate([
                'scenario_id' => 'required|exists:clinical_scenarios,id',
            ]);

            $session = \App\Models\SimulationSession::create([
                'user_id' => $request->user()->id,
                'scenario_id' => $validated['scenario_id'],
                'status' => 'active',
                'started_at' => now(),
            ]);

            \Illuminate\Support\Facades\Log::info('Session Created via Start Endpoint', ['session_id' => $session->id]);

            return response()->json($session->load('scenario'), 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Start Endpoint Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to initialize session'], 500);
        }
    }

    /**
     * Start a new simulation session (Resource Store).
     */
    public function store(Request $request)
    {
        return $this->start($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $session = \App\Models\SimulationSession::with(['scenario', 'turns'])->findOrFail($id);

        // Authorization check (ensure user owns session)
        if ($session->user_id !== request()->user()->id) {
            abort(403);
        }

        return response()->json($session);
    }

    /**
     * Handle chat interaction.
     */
    /**
     * Handle chat interaction.
     */
    public function chat(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $session = \App\Models\SimulationSession::with('scenario')->findOrFail($id);

        if ($session->user_id !== $request->user()->id) {
            abort(403);
        }

        // 1. User Turn
        $userTurn = $session->turns()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        // 2. AI Processing
        try {
            $aiResponse = $this->geminiService->handleSimulationTurn($session, $request->message);

            // 3. AI Turn
            $aiTurn = $session->turns()->create([
                'role' => 'ai',
                'content' => $aiResponse['answer'] ?? 'I apologize, I am having trouble responding right now.',
            ]);

            // 4. Save Thought Signature (Reasoning Trace)
            if (!empty($aiResponse['reasoning_trace'])) {
                \App\Models\ThoughtSignature::create([
                    'turn_id' => $aiTurn->id,
                    'reasoning_trace' => $aiResponse['reasoning_trace'],
                    'confidence' => 0.95, // Default for now
                    'tags' => [],
                ]);
            }

            // 5. Optionally update session state (e.g. status) if needed
            // if (isset($aiResponse['new_signature'])) { ... }

            return response()->json([
                'user_turn' => $userTurn,
                'ai_turn' => $aiTurn->load('session'), // Load session if needed by frontend
                'reasoning_trace' => $aiResponse['reasoning_trace'] ?? [],
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Simulation Chat Failed: ' . $e->getMessage());

            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'Quota')) {
                return response()->json(['error' => 'AI Usage Limit Reached. Please wait a moment.'], 429);
            }

            return response()->json(['error' => 'AI reasoning failed'], 500);
        }
    }

    /**
     * Handle a clinical query with Gemini 3.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleClinicalQuery(Request $request)
    {
        set_time_limit(120); // Allow 2 minutes for processing multimodal requests

        \Illuminate\Support\Facades\Log::info('Handling Clinical Query', [
            'has_attachment' => $request->hasFile('attachment'),
            'attachment_size' => $request->file('attachment')?->getSize(),
            'attachment_mime' => $request->file('attachment')?->getMimeType(),
            'message_length' => strlen($request->input('message')),
            'previous_signature' => $request->input('previous_thought_signature') ? 'present' : 'null',
        ]);

        $validated = $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'required_without:message|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
            'previous_thought_signature' => 'nullable|string',
        ]);

        $message = $validated['message'] ?? 'Please analyze this attached clinical document.';

        try {
            $response = $this->geminiService->handleClinicalQuery(
                $message,
                $request->file('attachment'),
                $validated['previous_thought_signature'] ?? null
            );

            // Save Interaction
            if ($request->user()) {
                \App\Models\AiInteraction::create([
                    'user_id' => $request->user()->id,
                    'prompt' => $message,
                    'ai_response' => $response,
                ]);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Clinical Query Controller Error: ' . $e->getMessage());

            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'Quota')) {
                return response()->json(['error' => 'AI Usage Limit Reached. Please wait a moment.'], 429);
            }

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Get a specific AI interaction.
     */
    public function getInteraction(Request $request, string $id)
    {
        $interaction = \App\Models\AiInteraction::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json($interaction);
    }

    /**
     * Get the last AI interaction for the user.
     */
    public function getLastInteraction(Request $request)
    {
        $interaction = \App\Models\AiInteraction::where('user_id', $request->user()->id)
            ->latest()
            ->first();

        if (!$interaction) {
            return response()->json(null); // No content
        }

        return response()->json($interaction);
    }
}
