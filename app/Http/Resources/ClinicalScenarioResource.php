<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClinicalScenarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'objective' => $this->objective,
            'complexity' => $this->complexity,
            'initial_patient_state' => $this->initial_patient_state,
            'medical_history' => $this->medical_history,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
