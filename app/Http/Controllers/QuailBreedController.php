<?php

namespace App\Http\Controllers;

use App\Helpers\QuailBreedHelper;
use App\Models\QuailBreed;
use Illuminate\Http\Request;

class QuailBreedController extends Controller
{
    public function index()
    {
        $breeds = QuailBreed::getActive();
        $currentBreed = QuailBreedHelper::getCurrentBreed();

        return view('quail-breed-management', compact('breeds', 'currentBreed'));
    }

    public function getCurrentBreed()
    {
        $breed = QuailBreedHelper::getCurrentBreed();
        return response()->json($breed);
    }

    public function getAllBreeds()
    {
        $breeds = QuailBreed::getActive();
        return response()->json($breeds);
    }

    public function setCurrentBreed(Request $request)
    {
        $request->validate([
            'breed_id' => 'required|exists:quail_breeds,id'
        ]);

        QuailBreedHelper::setCurrentBreed($request->breed_id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Breed updated successfully',
                'breed' => QuailBreedHelper::getCurrentBreed()
            ]);
        }

        return back()->with('success', 'Quail breed updated successfully!');
    }

    /**
     * Set current breed from quail detection result
     * This is called automatically when a breed is detected
     */
    public function setFromDetection(Request $request)
    {
        $request->validate([
            'breed_id' => 'required|exists:quail_breeds,id'
        ]);

        $breedId = $request->breed_id;

        // Set both singular and plural breed settings
        QuailBreedHelper::setCurrentBreed($breedId);
        QuailBreedHelper::setCurrentBreeds($breedId, $breedId);

        return response()->json([
            'success' => true,
            'message' => 'Breed updated from detection',
            'breed' => QuailBreedHelper::getCurrentBreed()
        ]);
    }
}