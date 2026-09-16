<?php

namespace App\Http\Controllers;

use App\Models\QuailBreed;
use App\Helpers\QuailBreedHelper;
use Illuminate\Http\Request;

class LearnBookController extends Controller
{
    /**
     * Display the Learn Book page with breed-specific content.
     */
    public function index()
    {
        // Get the currently selected breed with a safe fallback
        $currentBreed = QuailBreedHelper::getCurrentBreed();
        $currentBreeds = QuailBreedHelper::getCurrentBreeds();

        return view('feeder.learnbook', compact('currentBreed', 'currentBreeds'));
    }
}