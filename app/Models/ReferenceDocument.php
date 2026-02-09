<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenceDocument extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
