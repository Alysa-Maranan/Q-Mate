<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorReading;
use App\Helpers\QuailBreedHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TemperatureHumidityController extends Controller
{
    public function index()
    {
        $currentQuailBreed = QuailBreedHelper::getCurrentBreed();
        $currentBreeds = QuailBreedHelper::getCurrentBreeds();
        
        // Get latest readings for initial display
        $latest = SensorReading::latestReading()->first();
        
        // Get recent averages
        $avg24h = SensorReading::average(1440)->first(); // 24h avg
        
        // History for charts (last 24h)
                $history = SensorReading::recent(24)->orderBy('recorded_at', 'asc')->limit(100)->get();
        
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

    public function getPhilippineWeather()
    {
        // Check cache — 5-min for real API response, 10-min for fallback
        $cached = Cache::get('philippine_weather_data');
        if ($cached) {
            $cached['timestamp'] = now()->toIso8601String();
            return response()->json($cached);
        }

        try {
            $apiKey = env('OPENWEATHER_API_KEY', 'demo');
            $response = Http::timeout(5)->get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => 'Manila,PH',
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $result = [
                    'success' => true,
                    'temperature' => round($data['main']['temp'], 1),
                    'humidity' => round($data['main']['humidity'], 1),
                    'feels_like' => round($data['main']['feels_like'], 1),
                    'description' => $data['weather'][0]['description'] ?? 'N/A',
                    'location' => 'Manila, Philippines',
                    'timestamp' => now()->toIso8601String()
                ];
                Cache::put('philippine_weather_data', $result, 300); // 5 min cache
                return response()->json($result);
            }
        } catch (\Exception $e) {
            // Will use fallback below
        }

        // Fallback: Generate realistic Philippine weather data (cached 10 min)
        $fallback = Cache::get('philippine_weather_fallback');
        if ($fallback) {
            $fallback['timestamp'] = now()->toIso8601String();
            return response()->json($fallback);
        }

        $hour = now()->hour;
        $baseTemp = 27; // Average Manila temp

        if ($hour >= 6 && $hour < 12) {
            $temp = $baseTemp + rand(0, 3); // Morning: 27-30°C
        } elseif ($hour >= 12 && $hour < 17) {
            $temp = $baseTemp + rand(3, 6); // Afternoon: 30-33°C
        } elseif ($hour >= 17 && $hour < 20) {
            $temp = $baseTemp + rand(1, 3); // Evening: 28-30°C
        } else {
            $temp = $baseTemp - rand(1, 3); // Night: 24-26°C
        }

        $humidity = rand(65, 85); // Typical Philippine humidity

        $result = [
            'success' => true,
            'temperature' => $temp + (rand(-5, 5) / 10),
            'humidity' => $humidity,
            'feels_like' => $temp + rand(1, 3),
            'description' => $this->getWeatherDescription($temp, $humidity),
            'location' => 'Manila, Philippines (Simulated)',
            'timestamp' => now()->toIso8601String()
        ];
        Cache::put('philippine_weather_fallback', $result, 600); // 10 min cache
        return response()->json($result);
    }
    
    private function getWeatherDescription($temp, $humidity)
    {
        if ($temp > 32) return 'hot and humid';
        if ($temp > 30) return 'warm and humid';
        if ($temp > 27) return 'partly cloudy';
        if ($temp > 25) return 'pleasant';
        return 'cool';
    }
}

