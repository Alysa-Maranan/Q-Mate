<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug/servo', function() {
    $port = env('SERVO_SERIAL_PORT', 'COM4');
    $python = env('PYTHON_PATH', 'python');
    $script = base_path('scripts/servo_control.py');
    
    $command = "\"$python\" \"$script\" trigger 3 $port";
    
    echo "Command: $command\n\n";
    
    exec($command . ' 2>&1', $output, $returnCode);
    
    echo "Return Code: $returnCode\n";
    echo "Output:\n" . implode("\n", $output);
});