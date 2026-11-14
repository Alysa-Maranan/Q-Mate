<?php

use App\Http\Controllers\SensorDataController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Record temperature/humidity samples without needing an open browser.
 *
 * The scheduler is invoked once per minute, so the command takes six samples
 * ten seconds apart (0s, 10s, 20s, 30s, 40s, 50s). This works both with
 * `php artisan schedule:work` and a one-minute Render Cron Job.
 *
 * Run manually:
 *   php artisan sensor:record
 *   php artisan sensor:record --samples=6 --interval=10
 */
Artisan::command('sensor:record {--samples=1 : Number of samples to record} {--interval=10 : Seconds between samples}', function () {
    $samples = max(1, (int) $this->option('samples'));
    $interval = max(1, (int) $this->option('interval'));

    $lock = null;
    try {
        $lock = Cache::lock('sensor:record:command', 90);
        if (! $lock->get()) {
            $this->warn('Another sensor:record run is still active; skipping this run.');

            return 0;
        }
    } catch (\Throwable $e) {
        // Cache locking is unavailable; continue without it.
    }

    try {
        for ($i = 0; $i < $samples; $i++) {
            $reading = SensorDataController::sampleAndStore();

            if ($reading) {
                $this->info(sprintf(
                    '[%s] %s %.1f°C / %.1f%% (reading #%d)',
                    now()->toDateTimeString(),
                    SensorDataController::sourceLabel($reading->source),
                    (float) $reading->temperature,
                    (float) $reading->humidity,
                    $reading->id
                ));
            } else {
                $this->warn(sprintf(
                    '[%s] UNAVAILABLE - no fresh hardware or OpenWeather data',
                    now()->toDateTimeString()
                ));
            }

            if ($i < $samples - 1) {
                sleep($interval);
            }
        }
    } finally {
        if ($lock !== null) {
            try {
                $lock->release();
            } catch (\Throwable $e) {
                // Ignore release failures; the lock will expire on its own.
            }
        }
    }

    return 0;
})->purpose('Record temperature and humidity readings (DHT22 first, then OpenWeather)');

Schedule::command('feeder:check')
    ->everyMinute()
    ->withoutOverlapping();

// Six samples per minute = one new database row every ten seconds.
Schedule::command('sensor:record --samples=6 --interval=10')
    ->everyMinute();
