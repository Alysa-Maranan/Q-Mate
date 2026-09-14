<?php
// Simple test to check Python and serial setup

echo "<h1>🐍 Python Diagnostic</h1>";

// Check Python availability
echo "<h2>1. Python Check</h2>";
$python = 'python';
$output = [];
$returnCode = 0;
exec("$python --version 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    echo "<p style='color:green'>✅ Python found: " . implode("", $output) . "</p>";
} else {
    echo "<p style='color:red'>❌ Python NOT found</p>";
    echo "<p>Install Python or set PYTHON_PATH in .env</p>";
}

// Check pyserial
echo "<h2>2. Pyserial Check</h2>";
exec("$python -c \"import serial; print('pyserial installed')\" 2>&1", $output, $returnCode);
if ($returnCode === 0) {
    echo "<p style='color:green'>✅ pyserial installed</p>";
} else {
    echo "<p style='color:red'>❌ pyserial NOT installed</p>";
    echo "<p>Install by running: <code>pip install pyserial</code></p>";
}

// Check script exists
echo "<h2>3. Script Check</h2>";
$script = __DIR__ . '/../scripts/servo_control.py';
if (file_exists($script)) {
    echo "<p style='color:green'>✅ servo_control.py found</p>";
} else {
    echo "<p style='color:red'>❌ servo_control.py NOT found at: $script</p>";
}

// List COM ports (Windows)
echo "<h2>4. Available COM Ports (Windows)</h2>";
$ports = ['COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6'];
echo "<ul>";
foreach ($ports as $port) {
    // Try to open the port briefly
    $exists = file_exists($port);
    echo "<li>$port - " . ($exists ? "✅ exists" : "❌ not found") . "</li>";
}
echo "</ul>";

// Test running the script directly
echo "<h2>5. Direct Script Test</h2>";
$python = 'python';
$script = base_path('scripts/servo_control.php');
$script = base_path('scripts/servo_control.py');

if (file_exists($script)) {
    $command = "$python \"$script\" trigger 2 COM4";
    echo "<p>Running: <code>$command</code></p>";
    
    $output = [];
    $returnCode = 0;
    exec("$command 2>&1", $output, $returnCode);
    
    echo "<pre>";
    echo "Return Code: $returnCode\n";
    echo "Output:\n" . implode("\n", $output);
    echo "</pre>";
    
    if ($returnCode === 0) {
        echo "<p style='color:green'>✅ Servo triggered successfully!</p>";
    } else {
        echo "<p style='color:red'>❌ Servo trigger failed</p>";
    }
} else {
    echo "<p style='color:red'>❌ Script not found</p>";
}

echo "<h2>6. Quick Fix</h2>";
echo "<p>If pyserial is not installed, run this in command prompt:</p>";
echo "<pre>pip install pyserial</pre>";
