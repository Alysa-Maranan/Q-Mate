<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\QuailBreedHelper;
use App\Models\QuailBreed;
use App\Models\FarmSetting;

class SettingsController extends Controller
{
    public function index()
    {
        $currentBreed = QuailBreed::getCurrent();
        $currentBreeds = QuailBreedHelper::getCurrentBreeds();
        $breeds = QuailBreed::where('is_active', true)->orderBy('name')->get();
        $farm = [
            'name'             => FarmSetting::get('farm_name', "Escalona's Farm") ?: "Escalona's Farm",
            'address'          => FarmSetting::get('farm_address', 'Pagkakaisa, Naujan, Or. Mindoro') ?: 'Pagkakaisa, Naujan, Or. Mindoro',
            'phone'            => FarmSetting::get('farm_phone', '+63 917 123 4567') ?: '+63 917 123 4567',
            'email'            => FarmSetting::get('farm_email', 'escalona.farm@gmail.com') ?: 'escalona.farm@gmail.com',
            'live_quails'      => FarmSetting::get('live_quails', '0'),
            'dead_quails'      => FarmSetting::get('dead_quails', '0'),
            'dressed_quails_stock' => FarmSetting::get('dressed_quails_stock', '0'),
            'breed_start_date' => FarmSetting::get('breed_start_date', ''),
            'breed_end_date'   => FarmSetting::get('breed_end_date', ''),
        ];

        // Get feeding times
        $feedTimes = [
            'morning'   => FarmSetting::get('feed_time_morning', '08:00'),
            'afternoon' => FarmSetting::get('feed_time_afternoon', '12:00'),
            'evening'   => FarmSetting::get('feed_time_evening', '17:00'),
        ];
        
        return view('settings', [
            'currentBreed' => $currentBreed,
            'currentBreeds' => $currentBreeds,
            'breeds' => $breeds,
            'farm' => $farm,
            'feedTimes' => $feedTimes,
        ]);
    }

    public function saveFarmInfo(Request $request)
    {
        // Save farm information
        FarmSetting::set('farm_name', $request->farm_name ?? '');
        FarmSetting::set('farm_address', $request->farm_address ?? '');
        FarmSetting::set('farm_phone', $request->farm_phone ?? '');
        FarmSetting::set('farm_email', $request->farm_email ?? '');
        
        // Save quail inventory if provided
        if ($request->has('live_quails')) {
            FarmSetting::set('live_quails', $request->live_quails ?? 0);
        }
        if ($request->has('dead_quails')) {
            FarmSetting::set('dead_quails', $request->dead_quails ?? 0);
        }
        if ($request->has('dressed_quails_stock')) {
            FarmSetting::set('dressed_quails_stock', $request->dressed_quails_stock ?? 0);
        }
        if ($request->has('quail_count_date')) {
            FarmSetting::set('quail_count_date', $request->quail_count_date ?? date('Y-m-d'));
        }
        
        // Save breeds if provided (support for two breeds or single breed)
        if ($request->has('quail_breed_id_1') || $request->has('quail_breed_id_2')) {
            $breedId1 = $request->quail_breed_id_1 ?? null;
            $breedId2 = $request->quail_breed_id_2 ?? null;
            
            // At least one breed must be selected
            if ($breedId1 || $breedId2) {
                $breedId1 = $breedId1 ?: $breedId2; // Use second if first is empty
                $breedId2 = $breedId2 ?: $breedId1; // Use first if second is empty
                QuailBreedHelper::setCurrentBreeds($breedId1, $breedId2);
            }
        } elseif ($request->quail_breed_id) {
            // Fallback for single breed (backward compatibility)
            QuailBreedHelper::setCurrentBreed($request->quail_breed_id);
        }

        // Save breed dates
        if ($request->has('breed_start_date')) {
            FarmSetting::set('breed_start_date', $request->breed_start_date ?? '');
        }
        if ($request->has('breed_end_date')) {
            FarmSetting::set('breed_end_date', $request->breed_end_date ?? '');
        }
        
        // If updating quail inventory, redirect to inventory with tab. Otherwise redirect to settings
        if ($request->has('live_quails') || $request->has('dead_quails') || $request->has('dressed_quails_stock')) {
            return redirect()->route('inventory')->with('quail_updated', true);
        }
        
        return redirect()->route('settings')->with('farm_saved', true);
    }

    public function updateQuailStock(Request $request)
    {
        $data = $request->validate([
            'product_type' => 'required|in:live_quail,dressed_quail',
            'quantity' => 'required|integer|min:1',
        ]);

        $productType = $data['product_type'];
        $quantity = (int) $data['quantity'];

        if ($productType === 'live_quail') {
            $currentLive = (int) FarmSetting::get('live_quails', '0');
            if ($quantity > $currentLive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough live quails in stock.',
                ], 422);
            }

            $currentLive -= $quantity;
            FarmSetting::set('live_quails', (string) $currentLive);

            return response()->json([
                'success' => true,
                'data' => [
                    'live_quails' => $currentLive,
                    'dressed_quails_stock' => (int) FarmSetting::get('dressed_quails_stock', '0'),
                ],
            ]);
        }

        $currentDressed = (int) FarmSetting::get('dressed_quails_stock', '0');
        if ($quantity > $currentDressed) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough dressed quails in stock.',
            ], 422);
        }

        $currentDressed -= $quantity;
        FarmSetting::set('dressed_quails_stock', (string) $currentDressed);

        return response()->json([
            'success' => true,
            'data' => [
                'live_quails' => (int) FarmSetting::get('live_quails', '0'),
                'dressed_quails_stock' => $currentDressed,
            ],
        ]);
    }

    public function saveFeederSchedule(Request $request)
    {
        // Validate feeding times
        $request->validate([
            'feed_time_morning'   => 'required|date_format:H:i',
            'feed_time_afternoon' => 'required|date_format:H:i',
            'feed_time_evening'   => 'required|date_format:H:i',
        ]);

        // Check if any NEW time is in the past (only check changed times)
        $now = now();
        $currentTimes = [
            'Morning' => FarmSetting::get('feed_time_morning', '08:00'),
            'Afternoon' => FarmSetting::get('feed_time_afternoon', '12:00'),
            'Evening' => FarmSetting::get('feed_time_evening', '17:00'),
        ];
        
        $newTimes = [
            'Morning' => $request->feed_time_morning,
            'Afternoon' => $request->feed_time_afternoon,
            'Evening' => $request->feed_time_evening,
        ];

        foreach ($newTimes as $label => $time) {
            // Only validate if the time was changed
            if ($time !== $currentTimes[$label]) {
                $scheduleTime = \Carbon\Carbon::createFromFormat('H:i', $time)->setDateFrom($now);
                if ($scheduleTime->isPast()) {
                    return redirect()->route('settings')->with('error', "Cannot set {$label} feed time to a past time ({$time}).");
                }
            }
        }

        // Save feeding times to FarmSetting
        FarmSetting::set('feed_time_morning', $request->feed_time_morning);
        FarmSetting::set('feed_time_afternoon', $request->feed_time_afternoon);
        FarmSetting::set('feed_time_evening', $request->feed_time_evening);

        // Clear any cached feeding schedule
        \Illuminate\Support\Facades\Cache::forget('feeding_schedule');

        return redirect()->route('settings')->with('schedule_saved', true);
    }
}
