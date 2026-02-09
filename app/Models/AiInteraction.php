<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInteraction extends Model
{
    protected $fillable = ['user_id', 'prompt', 'ai_response'];

    protected $casts = [
        'ai_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
