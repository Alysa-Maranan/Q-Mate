<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale from session or use default
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        } else {
            app()->setLocale(config('app.locale', 'en'));
        }

        $this->startHardwareBridge();
    }

    private function startHardwareBridge(): void
    {
        if (PHP_OS_FAMILY !== 'Windows' || app()->runningInConsole()) {
            return;
        }

        if (!filter_var(env('HARDWARE_BRIDGE_AUTOSTART', true), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $startupLock = storage_path('logs/unified_bridge.starting');
        if (file_exists($startupLock)) {
            if (time() - filemtime($startupLock) < 60) {
                return;
            }
            @unlink($startupLock);
        }
        $startupHandle = @fopen($startupLock, 'x');
        if ($startupHandle === false) {
            return;
        }
        fclose($startupHandle);

        $instanceFile = storage_path('logs/unified_bridge.instance');
        if (file_exists($instanceFile)) {
            $instancePid = @file_get_contents($instanceFile);
            if ($instancePid === false) {
                return;
            }
            $instancePid = trim($instancePid);
            if ($instancePid !== '' && ctype_digit($instancePid)) {
                $processes = shell_exec('tasklist /FI "PID eq ' . $instancePid . '" /NH');
                if (is_string($processes) && str_contains($processes, $instancePid)) {
                    return;
                }
            }
            @unlink($instanceFile);
        }

        $script = base_path('scripts/unified_bridge.py');
        $port = env('SERVO_SERIAL_PORT', 'COM11');
        $log = storage_path('logs/unified_bridge.log');
        $python = env('PYTHON_PATH', 'python');
        $pythonw = str_ends_with(strtolower($python), '.exe')
            ? preg_replace('/python(?:\\.exe)?$/i', 'pythonw.exe', $python)
            : $python . 'w';

        if (!is_file($script)) {
            return;
        }

        $command = sprintf(
            'start "" /B "%s" "%s" %s >> "%s" 2>&1',
            $pythonw,
            $script,
            escapeshellarg($port),
            $log
        );
        pclose(popen($command, 'r'));
    }
}
