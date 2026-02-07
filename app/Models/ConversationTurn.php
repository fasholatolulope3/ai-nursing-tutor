<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationTurn extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_session_id',
        'role', // 'user' or 'ai'
        'content',
        'thought_signature_id', // Nullable, linked if AI
        'patient_state_snapshot_id', // Nullable
    ];

    public function session()
    {
        return $this->belongsTo(SimulationSession::class, 'simulation_session_id');
    }
}
