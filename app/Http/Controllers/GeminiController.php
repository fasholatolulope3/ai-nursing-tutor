<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Handle the incoming request.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:1',
            'system_instruction' => 'nullable|string',
            'thinking_level' => 'nullable|in:low,medium,high,LOW,MEDIUM,HIGH',
            'temperature' => 'nullable|numeric|min:0|max:2',
        ]);

        try {
            $prompt = $validated['prompt'];
            $options = [
                'system_instruction' => $validated['system_instruction'] ?? null,
                'thinking_level' => $validated['thinking_level'] ?? 'LOW',
                'temperature' => $validated['temperature'] ?? null,
            ];

            $responseContent = $this->geminiService->generateText($prompt, $options);

            return response()->json([
                'status' => 'success',
                'response' => $responseContent
            ]);

        } catch (Exception $e) {
            Log::error('Gemini Controller Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
