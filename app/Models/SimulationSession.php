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
        'scenario_id',
        'status',
        'score',
        'feedback_summary',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'feedback_summary' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scenario()
    {
        return $this->belongsTo(ClinicalScenario::class, 'scenario_id');
    }

    public function turns()
    {
        return $this->hasMany(ConversationTurn::class, 'simulation_session_id');
    }
}
