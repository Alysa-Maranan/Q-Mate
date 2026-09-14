<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartDht22Bridge extends Command
{
    protected $signature = 'dht22:start';
    protected $description = 'Start DHT22 bridge script in background';

    public function handle()
    {
        $script = base_path('scripts/dht22_bridge.py');
        $port   = env('SERVO_SERIAL_PORT', 'COM3');
        $log    = storage_path('logs/dht22_bridge.log');

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = "start /B pythonw \"{$script}\" {$port} > \"{$log}\" 2>&1";
            pclose(popen($cmd, 'r'));
        } else {
            $cmd = "nohup python3 \"{$script}\" {$port} > \"{$log}\" 2>&1 &";
            exec($cmd);
        }

        $this->info("DHT22 bridge started on {$port}. Log: {$log}");
    }
}
