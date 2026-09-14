<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FeedingSchedule;
use Carbon\Carbon;

echo "========== ACTIVE SCHEDULES ==========\n";
echo "Current Time: " . now()->format('Y-m-d H:i:s') . "\n\n";

$schedules = FeedingSchedule::all();

if ($schedules->isEmpty()) {
    echo "❌ No schedules found!\n";
} else {
    foreach ($schedules as $s) {
        echo "ID: {$s->id}\n";
        echo "  Time: {$s->time}\n";
        echo "  Amount: {$s->amount}s\n";
        echo "  Days: " . implode(', ', (array)$s->days) . "\n";
        echo "  Status: {$s->status}\n";
        echo "  Created: {$s->created_at}\n";
        echo "\n";
    }
}

echo "========== RECENT FEED HISTORY ==========\n";
$history = \App\Models\FeedHistory::latest('fed_at')->take(5)->get();

if ($history->isEmpty()) {
    echo "No feed history\n";
} else {
    foreach ($history as $h) {
        echo "Type: {$h->feed_type} | Duration: {$h->duration}s | Status: {$h->status} | Time: {$h->fed_at}\n";
    }
}
?>
