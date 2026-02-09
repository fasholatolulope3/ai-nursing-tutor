<?php

namespace App\Http\Controllers;

use App\Models\ClinicalScenario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScenarioController extends Controller
{
    public function show(string $id)
    {
        $scenario = ClinicalScenario::findOrFail($id);

        return Inertia::render('Dashboard/ScenarioDetails', [
            'scenario' => $scenario,
        ]);
    }
}
