<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeedingSchedule;
use App\Models\FeedHistory;

echo "=== ALL SCHEDULES ===\n";
$schedules = FeedingSchedule::all();
foreach ($schedules as $s) {
    echo "ID: " . $s->id . " | Time: " . $s->time . " | Status: " . $s->status . " | Last feed: " . ($s->last_feed_at ?? 'Never') . "\n";
}

echo "\n=== FEED HISTORY ===\n";
$history = FeedHistory::latest()->take(5)->get();
foreach ($history as $h) {
    echo "Type: " . $h->feed_type . " | Status: " . $h->status . " | Fed at: " . $h->fed_at . "\n";
}

echo "\n=== CURRENT TIME ===\n";
echo "Now: " . date('Y-m-d H:i:s') . "\n";
