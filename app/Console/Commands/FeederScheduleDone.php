<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedingSchedule;
use App\Services\FeedHistoryRecorder;

class FeederScheduleDone extends Command
{
    protected $signature = 'feeder:schedule-done {--id=} {--duration=5}';
    protected $description = 'Mark scheduled feed as done after servo finishes';

    public function handle(FeedHistoryRecorder $recorder)
    {
        $id       = $this->option('id');
        $duration = (int) $this->option('duration');

        // Wait for servo to finish (duration + 5s buffer for boot)
        sleep($duration + 5);

        $schedule = FeedingSchedule::find($id);
        if (!$schedule) return 1;

        $recorder->completeScheduled($schedule, $duration);

        return 0;
    }
}
