<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeederCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'schedule_id',
        'manual_feed_id',
        'duration',
        'status',
        'picked_up_at',
        'completed_at',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ─── Relationships ───

    public function schedule()
    {
        return $this->belongsTo(FeedingSchedule::class, 'schedule_id');
    }

    public function manualFeed()
    {
        return $this->belongsTo(ManualFeed::class, 'manual_feed_id');
    }

    // ─── Query Helpers ───

    /**
     * Get the oldest pending command (FIFO)
     */
    public static function getNextPending(): ?self
    {
        return self::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Queue a new scheduled feed command
     */
    public static function queueScheduled(int $scheduleId, int $duration): self
    {
        // Cancel any existing pending commands for this schedule
        self::where('schedule_id', $scheduleId)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        return self::create([
            'type' => 'scheduled',
            'schedule_id' => $scheduleId,
            'duration' => $duration,
            'status' => 'pending',
        ]);
    }

    /**
     * Queue a new manual feed command
     */
    public static function queueManual(int $manualFeedId, int $duration): self
    {
        return self::create([
            'type' => 'manual',
            'manual_feed_id' => $manualFeedId,
            'duration' => $duration,
            'status' => 'pending',
        ]);
    }

    /**
     * Mark command as picked up by ESP32
     */
    public function markPickedUp(): void
    {
        $this->update([
            'status' => 'in_progress',
            'picked_up_at' => now(),
        ]);
    }

    /**
     * Mark command as completed by ESP32
     */
    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Clean up stale commands (pending for more than 5 minutes)
     */
    public static function cleanupStale(): int
    {
        return self::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(5))
            ->update(['status' => 'failed']);
    }
}
