<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicalScenario;
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ClinicalScenario::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $scenario = ClinicalScenario::findOrFail($id);
        return response()->json($scenario);
    }
}
