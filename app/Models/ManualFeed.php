<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualFeed extends Model
{
    protected $fillable = [
        'duration',
        'cage_number',
        'days',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'days' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
