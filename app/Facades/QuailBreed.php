<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class QuailBreed extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'quail.breed';
    }
}