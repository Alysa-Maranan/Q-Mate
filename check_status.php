<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeedingSchedule;

$schedule = FeedingSchedule::find(21);

if ($schedule) {
    echo "Schedule ID: " . $schedule->id . "\n";
    echo "Time: " . $schedule->time . "\n";
    echo "Status: " . $schedule->status . "\n";
    echo "Last feed at: " . ($schedule->last_feed_at ?? 'Never') . "\n";
    echo "Updated at: " . $schedule->updated_at . "\n";
} else {
    echo "Schedule not found!\n";
}
