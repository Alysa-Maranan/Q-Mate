<?php

namespace App\Http\Controllers;

use App\Helpers\QuailBreedHelper;
use App\Models\SensorReading;

class TemperatureHumidityController extends Controller
{
    public function index()
    {
        $currentQuailBreed = QuailBreedHelper::getCurrentBreed();
        $currentBreeds = QuailBreedHelper::getCurrentBreeds();

        // Take one sample for this ten-second cycle so the first paint reflects
        // the same hardware/weather logic used by the live API and scheduler.
        $latest = SensorDataController::sampleAndStore();

        // Get recent averages
        $avg24h = SensorReading::average(1440)
            ->whereIn('source', ['hardware', 'actual', 'weather', 'openweather'])
            ->first(); // 24h avg

        // History for charts (last 24h)
        $history = SensorReading::recent(24)
            ->whereNotNull('temperature')
            ->whereNotNull('humidity')
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        $historyCount = SensorReading::count();

        return view('temperature-humidity', compact(
            'currentQuailBreed',
            'currentBreeds',
            'latest',
            'avg24h',
            'history',
            'historyCount'
        ));
    }

    /**
     * Real OpenWeather data for Naujan, Oriental Mindoro.
     * Returns success=false when the API key is invalid or the API is down.
     * Deliberately has no simulated fallback.
     */
    public function getPhilippineWeather()
    {
        $weather = SensorDataController::fetchWeather();

        if (! $weather) {
            return response()->json([
                'success' => false,
                'source' => SensorDataController::SOURCE_UNAVAILABLE,
                'label' => 'UNAVAILABLE',
                'message' => 'OpenWeather data is currently unavailable. Configure a valid OPENWEATHER_API_KEY.',
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        return response()->json([
            'success' => true,
            'temperature' => $weather['temperature'],
            'humidity' => $weather['humidity'],
            'feels_like' => $weather['feels_like'],
            'description' => $weather['description'],
            'location' => $weather['location'],
            'source' => SensorDataController::SOURCE_WEATHER,
            'label' => 'WEATHER',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
