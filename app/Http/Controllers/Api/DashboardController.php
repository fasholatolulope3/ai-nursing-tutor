<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        // In a real application, you would fetch these from the database
        // based on the authenticated user's activity.

        return response()->json([
            'scenarios_completed' => [
                'value' => '12',
                'trend' => '+3',
            ],
            'clinical_accuracy' => [
                'value' => '94%',
                'trend' => '+2%',
            ],
            'time_spent' => [
                'value' => '14h',
                'trend' => '+2h',
            ],
            'experience_points' => [
                'value' => '2,450',
                'trend' => '+150',
            ],
        ]);
    }
}
