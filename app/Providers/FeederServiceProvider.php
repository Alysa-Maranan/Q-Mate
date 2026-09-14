<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class FeederServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Register web routes located at routes/feeder.php
        Route::middleware('web')->group(base_path('routes/feeder.php'));

        // Schedule is now defined in routes/console.php
    }
}
