<?php
// Simple performance test
$start = microtime(true);

// Test basic Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/', 'GET')
);

$end = microtime(true);
$time = ($end - $start) * 1000; // Convert to milliseconds

echo "Website loading time: " . round($time, 2) . " ms\n";

if ($time < 500) {
    echo "✅ FAST - Website is loading quickly!\n";
} elseif ($time < 1000) {
    echo "⚠️ MODERATE - Website loading is acceptable\n";
} else {
    echo "❌ SLOW - Website needs optimization\n";
}

echo "Response status: " . $response->getStatusCode() . "\n";