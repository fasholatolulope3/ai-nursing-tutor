<?php

namespace App\Http\Controllers;

use App\Models\SimulationHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Simulations
        $simulations = \App\Models\SimulationSession::with('scenario')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($session) {
                $startedAt = $session->started_at ?? $session->created_at;
                return [
                    'id' => $session->id,
                    'type' => 'simulation',
                    'title' => $session->scenario->title ?? 'Unknown Scenario',
                    'subtitle' => $session->scenario->complexity . ' Clinical Simulation' ?? 'Simulation',
                    'difficulty' => $session->scenario->complexity ?? 'Medium',
                    'date' => $startedAt->format('M j, Y'),
                    'datetime' => $startedAt,
                    'time_ago' => $startedAt->diffForHumans(),
                    'duration' => $session->completed_at
                        ? $startedAt->diffInMinutes($session->completed_at) . 'm'
                        : 'In Progress',
                    'outcome' => $session->score ? $session->score . '%' : $session->status,
                    'link' => "/simulation/{$session->id}",
                ];
            });

        // 2. AI Interactions (Dashboard Chat)
        $aichats = \App\Models\AiInteraction::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'type' => 'ai_chat',
                    'title' => 'Clinical Assistant Query',
                    'subtitle' => \Illuminate\Support\Str::limit($chat->prompt, 50),
                    'difficulty' => 'N/A',
                    'date' => $chat->created_at->format('M j, Y'),
                    'datetime' => $chat->created_at,
                    'time_ago' => $chat->created_at->diffForHumans(),
                    'duration' => '1m', // Approx
                    'outcome' => 'Completed',
                    'link' => "/dashboard?interaction={$chat->id}",
                ];
            });

        // 3. Merge and Sort
        $history = $simulations->concat($aichats)
            ->sortByDesc('datetime')
            ->values();

        // Pagination (Manual)
        $perPage = 10;
        $page = $request->input('page', 1);
        $total = $history->count();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $history->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Dashboard/History', [
            'history' => [
                'data' => $paginated->items(),
                'links' => $paginated->linkCollection()->toArray(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
    public function destroy(Request $request, $id)
    {
        $type = $request->input('type');

        if ($type === 'simulation') {
            \App\Models\SimulationSession::where('user_id', $request->user()->id)
                ->where('id', $id)
                ->delete();
        } elseif ($type === 'ai_chat') {
            \App\Models\AiInteraction::where('user_id', $request->user()->id)
                ->where('id', $id)
                ->delete();
        }

        return redirect()->back()->with('success', 'History item deleted successfully.');
    }
}
