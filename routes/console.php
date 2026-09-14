<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NOTE: Laravel's built-in scheduler doesn't support sub-minute intervals
// Use start_exact_feeding.bat instead which runs the command every 5 seconds
// This ensures EXACT timing when scheduled time arrives
