<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\QuailBreedHelper;
use Illuminate\Support\Facades\View;

class QuailBreedServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the helper as a singleton
        $this->app->singleton('quail.breed', function () {
            return new QuailBreedHelper();
        });
    }

    public function boot()
    {
        // Share current breed info with all views
        View::composer('*', function ($view) {
            $currentBreed = QuailBreedHelper::getCurrentBreed();
            $currentBreeds = QuailBreedHelper::getCurrentBreeds();
            $view->with('currentQuailBreed', $currentBreed);
            $view->with('currentBreeds', $currentBreeds);
        });
    }
}