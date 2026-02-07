<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SimulationSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'clinical_scenario_id',
        'status',
        'start_time',
        'end_time',
        'final_score',
        'feedback_summary',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'feedback_summary' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scenario()
    {
        return $this->belongsTo(ClinicalScenario::class, 'clinical_scenario_id');
    }

    public function turns()
    {
        return $this->hasMany(ConversationTurn::class);
    }
}
