<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = $request->user()->supportTickets()->create($validated);

        return response()->json([
            'message' => 'Support request submitted successfully.',
            'ticket' => $ticket,
        ], 201);
    }
}
