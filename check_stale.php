<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Stale Records Check ===" . PHP_EOL;

$feedingNow = App\Models\FeedingSchedule::where('status', 'feeding_now')->get();
echo "Schedules with status=feeding_now: " . $feedingNow->count() . PHP_EOL;
foreach ($feedingNow as $s) {
    echo "  Schedule #{$s->id} time={$s->time} updated_at={$s->updated_at}" . PHP_EOL;
}

$doneFeed = App\Models\FeedingSchedule::where('status', 'done_feeding')->get();
echo "Schedules with status=done_feeding: " . $doneFeed->count() . PHP_EOL;
foreach ($doneFeed as $s) {
    echo "  Schedule #{$s->id} time={$s->time} updated_at={$s->updated_at}" . PHP_EOL;
}

$pendingCmds = App\Models\FeederCommand::whereIn('status', ['pending', 'in_progress'])->get();
echo "Commands pending/in_progress: " . $pendingCmds->count() . PHP_EOL;
foreach ($pendingCmds as $c) {
    echo "  Command #{$c->id} type={$c->type} status={$c->status} created={$c->created_at}" . PHP_EOL;
}

$allCmds = App\Models\FeederCommand::orderBy('id', 'desc')->take(5)->get();
echo "Last 5 commands:" . PHP_EOL;
foreach ($allCmds as $c) {
    echo "  Command #{$c->id} type={$c->type} status={$c->status} duration={$c->duration} created={$c->created_at} completed={$c->completed_at}" . PHP_EOL;
}
