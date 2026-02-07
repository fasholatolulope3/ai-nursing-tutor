<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicalScenario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'objective',
        'complexity',
        'initial_patient_state',
        'medical_history',
    ];

    protected $casts = [
        'objective' => 'array',
        'initial_patient_state' => 'array',
        'medical_history' => 'array',
    ];
}
