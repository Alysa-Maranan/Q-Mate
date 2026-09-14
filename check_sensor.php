<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$count = \App\Models\SensorReading::count();
echo "Total readings: " . $count . "\n";

if ($count > 0) {
    $latest = \App\Models\SensorReading::latest()->first();
    echo "Latest: " . $latest->temperature . "°C, " . $latest->humidity . "% at " . $latest->recorded_at . "\n";
    
    echo "\n--- Last 5 readings ---\n";
    $readings = \App\Models\SensorReading::latest()->limit(5)->get();
    foreach ($readings as $r) {
        echo "ID: " . $r->id . " | Temp: " . $r->temperature . "°C | Hum: " . $r->humidity . "% | " . $r->recorded_at . "\n";
    }
} else {
    echo "No readings found in database.\n";
}
