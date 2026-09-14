<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeedingSchedule;

// Get current time
$now = new DateTime();
$nextMinute = clone $now;
$nextMinute->modify('+1 minute');
$nextMinuteTime = $nextMinute->format('H:i:00');

echo "Current time: " . $now->format('h:i A') . "\n";
echo "Creating schedule for: " . $nextMinute->format('h:i A') . " (next minute)\n\n";

// Delete old schedules
FeedingSchedule::where('time', '<', '15:10:00')->delete();

// Create new schedule for next minute
$schedule = FeedingSchedule::create([
    'time' => $nextMinuteTime,
    'days' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'], // All days
    'amount' => 5,
    'enabled' => true,
    'status' => 'idle'
]);

echo "✅ Schedule created!\n";
echo "   ID: " . $schedule->id . "\n";
echo "   Time: " . $schedule->time . "\n";
echo "   Amount: 5 seconds\n\n";

echo "⏰ Maghintay ng " . $nextMinute->format('h:i A') . "...\n";
echo "🎯 Dapat mag-trigger sa exact na oras na ito!\n";
echo "💡 Tandaan: Kailangan naka-run ang 'php artisan schedule:work'\n";
