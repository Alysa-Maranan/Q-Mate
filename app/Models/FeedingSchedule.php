<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
        'days',
        'amount',
        'cage_number',
        'enabled',
        'status',
        'last_feed_at',
        'feeding_started_at',
    ];

    protected $casts = [
        'days' => 'array',
        'enabled' => 'boolean',
        'last_feed_at' => 'datetime',
        'feeding_started_at' => 'datetime',
    ];
}
