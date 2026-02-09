<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SimulationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scenario_title',
        'scenario_type',
        'difficulty',
        'score_or_outcome',
        'duration_minutes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
