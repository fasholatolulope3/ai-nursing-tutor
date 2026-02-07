<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationTurn;
use App\Models\SimulationSession;
use App\Services\AI\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimulationController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Start a new simulation session.
     */
    public function start(Request $request)
    {
        $request->validate([
            'scenario_id' => 'required|exists:clinical_scenarios,id',
        ]);

        $session = SimulationSession::create([
            'user_id' => Auth::id(),
            'clinical_scenario_id' => $request->scenario_id,
            'status' => 'active',
            'start_time' => now(),
        ]);

        // Load scenario
        $session->load('scenario');

        return response()->json($session, 201);
    }

    /**
     * Get an existing session.
     */
    public function show($id)
    {
        $session = SimulationSession::with(['scenario', 'turns'])->where('user_id', Auth::id())->findOrFail($id);
        return response()->json($session);
    }

    /**
     * Send a message to the simulation (Chat).
     */
    public function chat(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $session = SimulationSession::with(['scenario', 'turns'])->where('user_id', Auth::id())->findOrFail($id);

        // 1. Record User Turn
        $userTurn = ConversationTurn::create([
            'simulation_session_id' => $session->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        // 2. Call AI Service
        try {
            // Prepare context
            $context = [
                'scenario_title' => $session->scenario->title,
                'vitals' => $session->scenario->initial_patient_state, // Simplification: using initial state for now
            ];

            // Get history (last 10 turns)
            $history = $session->turns()->latest()->take(10)->get()->reverse()->map(function ($turn) {
                return ['role' => $turn->role, 'content' => $turn->content];
            })->toArray();

            $aiResponse = $this->gemini->generateResponse($history, $context);

            // 3. Record AI Turn
            $aiTurn = ConversationTurn::create([
                'simulation_session_id' => $session->id,
                'role' => 'ai',
                'content' => $aiResponse['content'],
                'thought_signature_id' => $aiResponse['thought_signature'] ? 1 : null, // Todo: store actual thought
            ]);

            // In a real app, we'd store the thought signature text in a separate table/column
            // For now we just return it in the response for debugging if needed

            return response()->json([
                'user_turn' => $userTurn,
                'ai_turn' => $aiTurn,
                'thought' => $aiResponse['thought_signature']
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'AI Service Unavailable'], 503);
        }
    }
}
