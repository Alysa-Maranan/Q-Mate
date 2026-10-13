<?php

namespace App\Services;

use App\Models\FeedHistory;
use App\Models\FeedingSchedule;
use App\Models\ManualFeed;
use Illuminate\Support\Facades\DB;

class FeedHistoryRecorder
{
    public function completeManual(ManualFeed $manualFeed, ?int $duration = null): bool
    {
        return DB::transaction(function () use ($manualFeed, $duration) {
            $feed = ManualFeed::query()->lockForUpdate()->find($manualFeed->id);

            if (!$feed || $feed->status !== 'feeding_now') {
                return false;
            }

            if (FeedHistory::where('manual_feed_id', $feed->id)->where('status', 'completed')->exists()) {
                $feed->update([
                    'status' => 'done_feeding',
                    'completed_at' => $feed->completed_at ?? now(),
                ]);

                return false;
            }

            $completedAt = now();
            $feed->update([
                'status' => 'done_feeding',
                'completed_at' => $completedAt,
            ]);

            FeedHistory::create([
                'feed_type' => 'manual',
                'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                'manual_feed_id' => $feed->id,
                'duration' => $duration ?? $feed->duration,
                'cage_number' => $feed->cage_number ?? 1,
                'days' => $feed->days,
                'status' => 'completed',
                'fed_at' => $completedAt,
            ]);

            return true;
        });
    }

    public function completeScheduled(FeedingSchedule $schedule, ?int $duration = null): bool
    {
        return DB::transaction(function () use ($schedule, $duration) {
            $feed = FeedingSchedule::query()->lockForUpdate()->find($schedule->id);

            if (!$feed || $feed->status !== 'feeding_now') {
                return false;
            }

            $startedAt = $feed->feeding_started_at ?? $feed->last_feed_at;
            if (!$startedAt) {
                return false;
            }

            FeedHistory::create([
                'feed_type' => 'scheduled',
                'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                'schedule_id' => $feed->id,
                'duration' => $duration ?? $feed->amount ?? 5,
                'cage_number' => $feed->cage_number ?? 1,
                'status' => 'completed',
                'fed_at' => now(),
            ]);

            $feed->update(['status' => 'done_feeding']);

            return true;
        });
    }
}
