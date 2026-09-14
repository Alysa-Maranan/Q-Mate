<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedHistory extends Model
{
    protected $table = 'feed_history';

    protected $fillable = [
        'feed_type',
        'feed_brand',
        'schedule_id',
        'manual_feed_id',
        'duration',
        'cage_number',
        'days',
        'status',
        'fed_at',
    ];

    protected $casts = [
        'days' => 'array',
        'fed_at' => 'datetime',
    ];

    // Relationships
    public function schedule()
    {
        return $this->belongsTo(FeedingSchedule::class, 'schedule_id');
    }

    public function manualFeed()
    {
        return $this->belongsTo(ManualFeed::class, 'manual_feed_id');
    }
}
