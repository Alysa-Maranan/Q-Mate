
<!DOCTYPE html>
<html>
<head>
    <title>Servo Diagnostics</title>
    <style>body{font-family:monospace;white-space:pre-wrap;}</style>
</head>
<body>
<h1>🦆 SQUIFM Servo Diagnostics</h1>
<hr>
<?php
echo "<b>[1] PHP Environment</b>\n";
echo "PHP: " . PHP_VERSION . "\n";
echo "CWD: " . getcwd() . "\n";
echo "Script exists: " . (file_exists('scripts/servo_control.py') ? '✅' : '❌') . "\n";

// Test 1: Python + pyserial
echo "\n<b>[2] Python + pyserial</b>\n";
$py_test = shell_exec('python -c "import serial; print(\'pyserial OK\')" 2>&1');
echo htmlspecialchars($py_test ?: '❌ No output');

// Test 2: Direct servo script
echo "\n<b>[3] Direct servo_control.py test</b>\n";
$port = isset($_GET['port']) ? htmlspecialchars($_GET['port']) : 'COM4';
$duration = isset($_GET['duration']) ? (int)$_GET['duration'] : 3;
$cmd = "python scripts\\servo_control.py trigger {$duration} {$port} 2>&1";
echo "CMD: $cmd\n";
$output = [];
$rc = 0;
exec($cmd, $output, $rc);
echo "RC: {$rc}\n";
echo "Output:\n" . implode("\n", array_map('htmlspecialchars', $output));

// Output check for OK response
$ok_found = false;
foreach ($output as $line) {
    if (stripos($line, 'OK') !== false) {
        $ok_found = true;
        break;
    }
}
if ($ok_found) {
    echo "<b style='color:green'>SUCCESS: Servo responded OK!</b>\n";
} else {
    echo "<b style='color:red'>ERROR: No OK response from servo.</b>\n";
}

// Test 3: Recent logs
echo "\n<b>[4] Laravel Logs (last 20 lines)</b>\n";
if (file_exists('storage/logs/laravel.log')) {
    $logs = file_get_contents('storage/logs/laravel.log');
    $lines = explode("\n", $logs);
    $recent = array_slice($lines, -20);
    echo htmlspecialchars(implode("\n", $recent));
} else {
    echo "❌ No laravel.log";
}
?>
<hr>
<form method="GET">
Port: <input name="port" value="<?php echo htmlspecialchars($port); ?>" placeholder="COM4">
Duration: <input name="duration" value="<?php echo htmlspecialchars($duration); ?>" type="number" min="1" max="10">
<input type="submit" value="Test Serial">
</form>
<p><b>Usage:</b> http://localhost/test-servo-diagnostics.php?port=COM3</p>
<p><b>Expected:</b> RC=0, ESP32 Response: "Servo OPEN... OK", Servo moves!</p>
</body>
</html>

