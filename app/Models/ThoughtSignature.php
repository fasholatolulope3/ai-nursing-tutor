<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThoughtSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'turn_id',
        'reasoning_trace',
        'confidence',
        'branching_factor',
        'tags',
    ];

    protected $casts = [
        'reasoning_trace' => 'array',
        'tags' => 'array',
    ];

    public function turn()
    {
        return $this->belongsTo(ConversationTurn::class, 'turn_id');
    }
}
