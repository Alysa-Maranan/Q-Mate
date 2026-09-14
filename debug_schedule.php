<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeedingSchedule;
use Carbon\Carbon;

echo "=== CURRENT TIME ===\n";
$now = Carbon::now();
echo "Now: " . $now->format('Y-m-d H:i:s') . "\n";
echo "Time (H:i:s): " . $now->format('H:i:s') . "\n";
echo "Day: " . strtolower($now->format('D')) . "\n\n";

echo "=== ALL SCHEDULES ===\n";
$schedules = FeedingSchedule::all();

if (count($schedules) == 0) {
    echo "❌ WALANG SCHEDULE! Mag-add muna ng schedule.\n";
} else {
    foreach ($schedules as $s) {
        echo "ID: " . $s->id . "\n";
        echo "  Time: " . $s->time . "\n";
        echo "  Days: " . json_encode($s->days) . "\n";
        echo  "  Amount: " . $s->amount . "\n";
        echo "  Enabled: " . ($s->enabled ? 'Yes' : 'No') . "\n";
        echo "  Status: " . $s->status . "\n";
        echo "  Last feed: " . ($s->last_feed_at ?? 'Never') . "\n";
        
        // Check if should trigger now
        $scheduleTime = Carbon::createFromFormat('H:i:s', $s->time)->format('H:i:00');
        $currentTime = $now->format('H:i:00');
        $todayKey = strtolower(substr($now->format('D'), 0, 3));
        $days = $s->days ?? [];
        
        echo "  Should trigger now? ";
        if ($scheduleTime === $currentTime) {
            echo "YES - Time matches! ";
            if (empty($days) || in_array($todayKey, $days)) {
                echo "AND Day matches!\n";
            } else {
                echo "BUT Day does NOT match!\n";
            }
        } else {
            echo "NO - Time doesn't match (Schedule: $scheduleTime vs Now: $currentTime)\n";
        }
        echo "\n";
    }
}
