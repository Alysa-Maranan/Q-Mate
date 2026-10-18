<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedingSchedule;
use App\Services\FeedHistoryRecorder;
use Illuminate\Support\Facades\Log;

class FeederScheduleDone extends Command
{
    protected $signature = 'feeder:schedule-done {--id=} {--duration=5} {--event-id=} {--simulation}';
    protected $description = 'Mark scheduled feed as done after servo finishes';

    public function handle(FeedHistoryRecorder $recorder)
    {
        $id       = $this->option('id');
        $duration = (int) $this->option('duration');
        $schedule = FeedingSchedule::find($id);
        if (!$schedule) return 1;

        $startedAt = $schedule->feeding_started_at ?? $schedule->last_feed_at;
        $finishAt = $startedAt
            ? $startedAt->copy()->addSeconds($duration + ($this->option('simulation') ? 0 : 5))
            : now();
        $remainingSeconds = max(0, $finishAt->getTimestamp() - now()->getTimestamp());
        if ($remainingSeconds > 0) {
            sleep($remainingSeconds);
        }

        $eventId = $this->option('event-id');
        $status = 'failed';

        if ($this->option('simulation')) {
            $status = 'simulated';
        } else {
            $resultPath = storage_path('logs/servo_result.json');
            $result = is_file($resultPath)
                ? json_decode((string) file_get_contents($resultPath), true)
                : null;

            if (
                is_array($result)
                && ($result['event_id'] ?? null) === $eventId
                && ($result['status'] ?? null) === 'in_progress'
            ) {
                sleep(30);
                $result = is_file($resultPath)
                    ? json_decode((string) file_get_contents($resultPath), true)
                    : null;
            }

            if (
                is_array($result)
                && ($result['event_id'] ?? null) === $eventId
                && ($result['success'] ?? false) === true
            ) {
                $status = 'completed';
            }
        }

        $recorded = $recorder->finalizeScheduledRun($schedule, $duration, $status);
        Log::info('Scheduled feeder run finalized', [
            'schedule_id' => $schedule->id,
            'event_id' => $eventId,
            'status' => $status,
            'recorded' => $recorded,
        ]);

        return 0;
    }
}
