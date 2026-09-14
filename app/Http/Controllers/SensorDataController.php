<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\SensorReading;

class SensorDataController extends Controller
{
    /**
     * Store sensor readings from WEMOS
     * POST /api/sensor-data
     * Body: { "temperature": 25.5, "humidity": 60.5 }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
        ]);

        // Store using Eloquent model
        $sensorReading = SensorReading::create([
            'temperature' => $validated['temperature'] ?? null,
            'humidity' => $validated['humidity'] ?? null,
            'recorded_at' => now(),
        ]);
        $reading = $sensorReading->id;

        Log::info('Sensor data recorded', [
            'temperature' => $validated['temperature'],
            'humidity' => $validated['humidity'],
        ]);

        return response()->json([
            'success' => true,
            'id' => $reading,
            'message' => 'Sensor data recorded',
        ]);
    }

    /**
     * Get latest sensor reading
     * GET /api/sensor-data/latest
     */
    public function getLatest()
    {
        $reading = SensorReading::latestReading()->first();

        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'No readings yet',
            ]);
        }

        return response()->json([
            'success' => true,
            'temperature' => (float) $reading->temperature,
            'humidity' => (float) $reading->humidity,
            'recorded_at' => $reading->recorded_at,
        ]);
    }

    /**
     * Get sensor readings for chart
     * GET /api/sensor-data/history?hours=24
     */
    public function getHistory(Request $request)
    {
        $hours = min((int) $request->query('hours', 24), 720); // Max 1 month
        
        $readings = SensorReading::recent($hours)
            ->orderBy('recorded_at', 'asc')
            ->limit(500)
            ->get()
            ->map(function ($reading) {
                return [
                    'id' => $reading->id,
                    'temperature' => (float) $reading->temperature,
                    'humidity' => (float) $reading->humidity,
                    'recorded_at' => $reading->recorded_at,
                ];
            });

        return response()->json([
            'success' => true,
            'hours' => $hours,
            'count' => $readings->count(),
            'data' => $readings,
        ]);
    }

    /**
     * Get average temperature and humidity
     * GET /api/sensor-data/average?minutes=60
     */
    public function getAverage(Request $request)
    {
        $minutes = (int) $request->query('minutes', 60);
        
        $stats = SensorReading::average($minutes)->first();

        return response()->json([
            'success' => true,
            'minutes' => $minutes,
            'avg_temperature' => (float) round($stats->avg_temperature, 2),
            'avg_humidity' => (float) round($stats->avg_humidity, 2),
        ]);
    }
}
