<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Insert a test schedule
\App\Models\FeedingSchedule::create([
    'time' => '08:00:00',
    'days' => json_encode(['mon','tue']),
    'amount' => 5,
]);

echo "Count: " . \App\Models\FeedingSchedule::count() . PHP_EOL;
