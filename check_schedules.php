<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$schedules = App\Models\FeedingSchedule::all();
echo "Total schedules: " . $schedules->count() . "\n\n";
foreach ($schedules as $s) {
    echo "ID:{$s->id} | time:{$s->time} | amount:{$s->amount} | status:{$s->status} | enabled:" . ($s->enabled ? 'Y' : 'N') . "\n";
}
