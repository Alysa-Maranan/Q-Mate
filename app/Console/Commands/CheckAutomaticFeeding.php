<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckAutomaticFeeding extends Command
{
    protected $signature = 'feeder:check-automatic';
    protected $description = 'Check saved feeding schedules and trigger the feeder';

    public function handle()
    {
        return $this->call('feeder:check');
    }
}
