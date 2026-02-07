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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scenario_id' => 'required|exists:clinical_scenarios,id',
        ]);

        $session = \App\Models\SimulationSession::create([
            'user_id' => $request->user()->id,
            'clinical_scenario_id' => $validated['scenario_id'],
            'status' => 'active',
            'start_time' => now(),
        ]);

        return response()->json($session->load('scenario'), 201);
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

        $session = \App\Models\SimulationSession::findOrFail($id);

        if ($session->user_id !== $request->user()->id) {
            abort(403);
        }

        // 1. User Turn
        $userTurn = $session->turns()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        // 2. AI Processing
        // $aiResponseContent = $this->geminiService->processTurn($session, $request->message);
        // For now, let's just echo back or use a simple response if processTurn isn't ready.
        // But we are focusing on handleClinicalQuery below.
        $aiResponseContent = "Simulation response placeholder.";

        // 3. AI Turn
        $aiTurn = $session->turns()->create([
            'role' => 'ai',
            'content' => $aiResponseContent,
        ]);

        return response()->json([
            'user_turn' => $userTurn,
            'ai_turn' => $aiTurn,
        ]);
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
            return response()->json($response);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Clinical Query Controller Error: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }
}
