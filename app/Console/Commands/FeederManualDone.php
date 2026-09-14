<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ManualFeed;
use App\Models\FeedHistory;

class FeederManualDone extends Command
{
    protected $signature = 'feeder:manual-done {--id=} {--duration=5}';
    protected $description = 'Mark manual feed as done after servo finishes';

    public function handle()
    {
        $id       = $this->option('id');
        $duration = (int) $this->option('duration');

        // Wait for servo to finish (duration + 5s buffer for boot)
        sleep($duration + 5);

        $manualFeed = ManualFeed::find($id);
        if (!$manualFeed) return 1;

        $manualFeed->update([
            'status'       => 'done_feeding',
            'completed_at' => now(),
        ]);

        FeedHistory::create([
            'feed_type'      => 'manual',
            'feed_brand'     => cache('feed_brand', 'Quail Layer Smash'),
            'manual_feed_id' => $manualFeed->id,
            'duration'       => $manualFeed->duration,
            'days'           => $manualFeed->days,
            'cage_number'    => $manualFeed->cage_number,
            'status'         => 'completed',
            'fed_at'         => now(),
        ]);

        return 0;
    }
}
