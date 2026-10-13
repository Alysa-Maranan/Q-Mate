<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ManualFeed;
use App\Services\FeedHistoryRecorder;

class FeederManualDone extends Command
{
    protected $signature = 'feeder:manual-done {--id=} {--duration=5}';
    protected $description = 'Mark manual feed as done after servo finishes';

    public function handle(FeedHistoryRecorder $recorder)
    {
        $id       = $this->option('id');
        $duration = (int) $this->option('duration');

        // Wait for servo to finish (duration + 5s buffer for boot)
        sleep($duration + 5);

        $manualFeed = ManualFeed::find($id);
        if (!$manualFeed) return 1;

        $recorder->completeManual($manualFeed, $duration);

        return 0;
    }
}
