<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedingScheduleController;
use App\Http\Controllers\LearnBookController;
use App\Http\Controllers\FeederController;

Route::get('/feeder', [FeederController::class, 'index'])
    ->name('feeder.index');

Route::get('/feeder/learnbook', [LearnBookController::class, 'index'])
    ->name('feeder.learnbook');

Route::get('/feeder/dht22-test', function () {
    return view('feeder.dht22-test');
})->name('feeder.dht22-test');

Route::get('/feeder/sensor-debug', function () {
    return view('feeder.sensor-debug');
})->name('feeder.sensor-debug');

Route::get('/feeder/schedules', [FeedingScheduleController::class, 'index']);

Route::post('/feeder/schedules', [FeedingScheduleController::class, 'store'])
    ->name('feeder.schedules.store');

Route::delete('/feeder/schedules/{feedingSchedule}', [
    FeedingScheduleController::class,
    'destroy'
])->name('feeder.schedules.destroy');

Route::post('/feeder/schedules/{feedingSchedule}/trigger', [
    FeedingScheduleController::class,
    'triggerNow'
]);

Route::get('/feeder/schedules/status', [
    FeedingScheduleController::class,
    'getStatus'
]);

Route::get('/feeder/schedules/auto-check', [
    FeedingScheduleController::class,
    'checkSchedule'
]);

Route::get('/feeder/test-animation', function () {
    return view('feeder.test-animation');
})->name('feeder.test-animation');

Route::get('/feeder/timing-test', function () {
    return view('feeder.timing-test');
})->name('feeder.timing-test');

Route::get('/feeder/manual/feed', function () {
    return redirect('/feeder');
});

Route::post('/feeder/manual/feed', [
    FeedingScheduleController::class,
    'manualFeed'
])->name('feeder.manual.feed');

Route::post('/feeder/toggle', [
    FeedingScheduleController::class,
    'toggleFeeder'
])->name('feeder.toggle');

Route::delete('/feeder/history/{feedHistory}', [
    FeedingScheduleController::class,
    'deleteHistory'
])->name('feeder.history.delete');

if (!function_exists('findPythonExecutable')) {
    function findPythonExecutable()
    {
        $candidates = [
            'python3',
            'python',
            'C:\\Python311\\python.exe',
            'C:\\Python310\\python.exe',
            'C:\\Python39\\python.exe',
            'C:\\Users\\' . get_current_user() . '\\AppData\\Local\\Programs\\Python\\Python311\\python.exe',
        ];

        foreach ($candidates as $cmd) {
            $output = [];
            $return = 0;

            @exec($cmd . ' --version 2>&1', $output, $return);

            if ($return === 0) {
                \Illuminate\Support\Facades\Log::info("Found Python: $cmd");
                return $cmd;
            }
        }

        return null;
    }
}

Route::get('/feeder/trigger-servo', function () {

    $python = findPythonExecutable() ?? 'python';
    $script = base_path('scripts/servo_dht22_web.py');

    if (!file_exists($script)) {
        return response()->json([
            'success' => false,
            'error' => 'Script not found',
            'checked' => $script
        ]);
    }

    $ports = [
        'COM4',
        'COM3',
        'COM5',
        'COM6',
        'COM1',
        'COM2'
    ];

    $triedPorts = [];
    $lastError = null;

    foreach ($ports as $port) {

        $triedPorts[] = $port;

        $command = "$python \"$script\" both 5 $port";

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            $command,
            $descriptors,
            $pipes,
            base_path(),
            null
        );

        if (is_resource($process)) {

            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            $returnCode = proc_close($process);

            \Illuminate\Support\Facades\Log::info(
                "Python execution on $port",
                [
                    'return_code' => $returnCode,
                    'output' => $output,
                    'errors' => $errors,
                ]
            );

            if ($returnCode === 0 && !empty($output)) {

                $result = json_decode($output, true);

                if ($result && isset($result['sensor']['data'])) {

                    $sensorData = $result['sensor']['data'];

                    \Illuminate\Support\Facades\DB::table('sensor_readings')
                        ->insert([
                            'temperature' => $sensorData['temperature'] ?? null,
                            'humidity' => $sensorData['humidity'] ?? null,
                            'recorded_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                return response()->json([
                    'success' => true,
                    'port' => $port,
                    'result' => $result,
                    'message' => 'Servo triggered! Sensors read successfully.'
                ]);
            }

            $lastError = [
                'port' => $port,
                'returnCode' => $returnCode,
                'output' => $output,
                'errors' => $errors,
            ];

        } else {

            $lastError = [
                'port' => $port,
                'error' => 'Failed to open process',
            ];
        }
    }

    return response()->json([
        'success' => false,
        'tried_ports' => $triedPorts,
        'last_error' => $lastError,
        'message' => 'Could not reach WEMOS. Check USB connection, Python, pyserial, and COM port.'
    ]);
})->name('feeder.trigger-servo');

Route::get('/feeder/read-sensors', function () {

    $python = findPythonExecutable() ?? 'python';
    $script = base_path('scripts/servo_dht22_web.py');

    if (!file_exists($script)) {
        return response()->json([
            'success' => false,
            'error' => 'Script not found'
        ]);
    }

    $ports = [
        'COM4',
        'COM3',
        'COM5',
        'COM6',
        'COM1',
        'COM2'
    ];

    $lastError = null;

    foreach ($ports as $port) {

        $command = "$python \"$script\" sensor $port";

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            $command,
            $descriptors,
            $pipes,
            base_path(),
            null
        );

        if (is_resource($process)) {

            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            $returnCode = proc_close($process);

            if ($returnCode === 0 && !empty($output)) {

                $result = json_decode($output, true);

                if ($result && isset($result['data'])) {

                    $sensorData = $result['data'];

                    \Illuminate\Support\Facades\DB::table('sensor_readings')
                        ->insert([
                            'temperature' => $sensorData['temperature'] ?? null,
                            'humidity' => $sensorData['humidity'] ?? null,
                            'recorded_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                return response()->json($result);
            }

            $lastError = [
                'port' => $port,
                'returnCode' => $returnCode,
                'output' => $output,
                'errors' => $errors,
            ];

        } else {

            $lastError = [
                'port' => $port,
                'error' => 'Failed to open process',
            ];
        }
    }

    return response()->json([
        'success' => false,
        'last_error' => $lastError,
        'message' => 'Could not reach WEMOS. Check USB connection, Python, pyserial, and COM port.'
    ]);
})->name('feeder.read-sensors');

Route::get('/feeder/test-servo', function () {

    $script = base_path('scripts/servo_control.py');

    if (!file_exists($script)) {
        return response()->json([
            'success' => false,
            'error' => 'Script not found',
            'checked' => $script
        ]);
    }

    $ports = [
        'COM4',
        'COM3',
        'COM5',
        'COM6',
        'COM1',
        'COM2'
    ];

    $triedPorts = [];
    $output = [];
    $returnCode = 1;

    foreach ($ports as $port) {

        $triedPorts[] = $port;

        $python = findPythonExecutable() ?? 'python';

        $command = "$python \"$script\" trigger 5 $port";

        $currentOutput = [];

        exec(
            $command . ' 2>&1',
            $currentOutput,
            $returnCode
        );

        $output = array_merge(
            $output,
            $currentOutput
        );

        if ($returnCode === 0) {

            return response()->json([
                'success' => true,
                'port' => $port,
                'output' => implode("\n", $currentOutput),
                'message' => 'Servo triggered successfully!'
            ]);
        }
    }

    return response()->json([
        'success' => false,
        'tried_ports' => $triedPorts,
        'output' => implode("\n", $output),
        'returnCode' => $returnCode,
        'message' => 'No servo found on any port. Make sure ESP32 is connected via USB.'
    ]);
});