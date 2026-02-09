<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReferenceDocument;
use App\Models\ClinicalScenario;

class RecommendationsController extends Controller
{
    /**
     * Get recommendations based on topics.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'topics' => 'array',
            'topics.*' => 'string'
        ]);

        $topics = $request->input('topics', []);

        if (empty($topics)) {
            // Default to something if no topics are provided, maybe latest general
            return response()->json([
                'scenarios' => [],
                'references' => []
            ]);
        }

        // Logic: Find items where tags overlap with topics
        // JSON_CONTAINS or LIKE query. For array overlap, JSON_OVERLAPS is ideal in MySQL 8, 
        // but simple LIKE '%topic%' on JSON string is a fallback for older versions or portability.
        // Or we can simple use whereJsonContains if looking for exact match.
        // Let's use whereJsonContains loop for ANY match.

        // Scenarios
        $scenariosQuery = ClinicalScenario::query();
        $scenariosQuery->where(function ($q) use ($topics) {
            foreach ($topics as $topic) {
                $q->orWhereJsonContains('tags', strtolower($topic));
            }
        });
        $scenarios = $scenariosQuery->take(3)->get();

        // References
        $referencesQuery = ReferenceDocument::query();
        $referencesQuery->where(function ($q) use ($topics) {
            foreach ($topics as $topic) {
                $q->orWhereJsonContains('tags', strtolower($topic));
            }
        });
        $references = $referencesQuery->take(3)->get();

        return response()->json([
            'scenarios' => $scenarios,
            'references' => $references
        ]);
    }
}
