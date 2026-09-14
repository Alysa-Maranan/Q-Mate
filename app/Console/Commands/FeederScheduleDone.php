<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedingSchedule;
use App\Models\FeedHistory;

class FeederScheduleDone extends Command
{
    protected $signature = 'feeder:schedule-done {--id=} {--duration=5}';
    protected $description = 'Mark scheduled feed as done after servo finishes';

    public function handle()
    {
        $id       = $this->option('id');
        $duration = (int) $this->option('duration');

        // Wait for servo to finish (duration + 5s buffer for boot)
        sleep($duration + 5);

        $schedule = FeedingSchedule::find($id);
        if (!$schedule) return 1;

        $schedule->update(['status' => 'done_feeding']);

        FeedHistory::create([
            'feed_type'   => 'scheduled',
            'feed_brand'  => cache('feed_brand', 'Quail Layer Smash'),
            'schedule_id' => $schedule->id,
            'duration'    => $duration,
            'cage_number' => $schedule->cage_number ?? 1,
            'status'      => 'completed',
            'fed_at'      => now(),
        ]);

        return 0;
    }
}
