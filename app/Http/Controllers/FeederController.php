<?php

namespace App\Http\Controllers;

use App\Helpers\QuailBreedHelper;
use Illuminate\Http\Request;

class FeederController extends Controller
{
    public function index()
    {
        // Make sure the daily feeding schedules (8:00 / 12:00 / 5:00 with
        // 5-minute duration) exist and are up to date.
        FeedingScheduleController::ensureDailySchedulesExist();

        $currentBreeds = QuailBreedHelper::getCurrentBreeds();
        
        return view('feeder.index', compact('currentBreeds'));
    }
}
