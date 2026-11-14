<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    public const SOURCE_HARDWARE = 'hardware';

    public const SOURCE_WEATHER = 'weather';

    public const SOURCE_UNAVAILABLE = 'unavailable';

    /**
     * A real DHT22 reading remains the preferred source for this many seconds.
     */
    public const HARDWARE_FRESH_SECONDS = 30;

    /**
     * Short guard so the browser poll and the scheduler cannot write twice in
     * the same ten-second cycle. It is time-based only: identical values in a
     * later cycle are still always saved as a new row.
     */
    public const SAMPLE_GUARD_SECONDS = 8;

    /**
     * OpenWeather current conditions update infrequently, so cache the real
     * API response briefly and still create a new database row every cycle.
     */
    public const WEATHER_CACHE_SECONDS = 55;

    private const HARDWARE_CACHE_KEY = 'sensor:hardware:last-seen';

    private const WEATHER_CACHE_KEY = 'sensor:weather:current';

    private const SAMPLE_LOCK_KEY = 'sensor:sampling';

    /**
     * Store a real sensor reading from the WEMOS/DHT22 bridge (or manual entry).
     * POST /api/sensor-data
     * POST /api/sensor-data/manual-entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric|between:-40,80',
            'humidity' => 'required|numeric|between:0,100',
            // Simulated/fake payloads are intentionally not accepted.
            'source' => 'nullable|string|in:hardware,actual,weather,openweather',
        ]);

        $source = self::normalizeSource($validated['source'] ?? null);
        if ($source === self::SOURCE_UNAVAILABLE) {
            $source = self::SOURCE_HARDWARE;
        }

        $reading = SensorReading::create([
            'temperature' => round((float) $validated['temperature'], 1),
            'humidity' => round((float) $validated['humidity'], 1),
            'source' => $source,
            'recorded_at' => now(),
        ]);

        // Remember the last *actual* hardware report separately from the rows
        // we copy during each ten-second cycle. This lets the sampler expire
        // hardware data after 30 seconds instead of keeping copies alive.
        if ($source === self::SOURCE_HARDWARE) {
            self::rememberHardwareReading(
                (float) $reading->temperature,
                (float) $reading->humidity
            );
        }

        Log::info('Sensor data recorded', [
            'id' => $reading->id,
            'temperature' => $reading->temperature,
            'humidity' => $reading->humidity,
            'source' => $source,
        ]);

        return response()->json([
            'success' => true,
            'id' => $reading->id,
            'temperature' => (float) $reading->temperature,
            'humidity' => (float) $reading->humidity,
            'source' => $reading->source,
            'label' => self::sourceLabel($reading->source),
            'recorded_at' => $reading->recorded_at,
            'message' => 'Sensor data recorded',
        ]);
    }

    /**
     * Return the latest reading and record a new sample for this ten-second
     * cycle. Hardware is used while it is fresh; otherwise real OpenWeather
     * data is used. If neither exists, no fake row is written.
     *
     * GET /api/sensor-data/latest
     */
    public function getLatest()
    {
        $reading = self::sampleAndStore();

        if (! $reading) {
            return response()->json([
                'success' => false,
                'temperature' => null,
                'humidity' => null,
                'source' => self::SOURCE_UNAVAILABLE,
                'label' => 'UNAVAILABLE',
                'recorded_at' => null,
                'message' => 'No fresh hardware or OpenWeather data available',
            ]);
        }

        return response()->json([
            'success' => true,
            'temperature' => (float) $reading->temperature,
            'humidity' => (float) $reading->humidity,
            'source' => self::normalizeSource($reading->source),
            'label' => self::sourceLabel($reading->source),
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
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($reading) {
                return [
                    'id' => $reading->id,
                    'temperature' => $reading->temperature !== null ? (float) $reading->temperature : null,
                    'humidity' => $reading->humidity !== null ? (float) $reading->humidity : null,
                    'source' => self::normalizeSource($reading->source),
                    'label' => self::sourceLabel($reading->source),
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

        $stats = SensorReading::average($minutes)
            ->whereIn('source', ['hardware', 'actual', 'weather', 'openweather'])
            ->first();

        $avgTemperature = ($stats && $stats->avg_temperature !== null)
            ? round((float) $stats->avg_temperature, 2)
            : null;

        $avgHumidity = ($stats && $stats->avg_humidity !== null)
            ? round((float) $stats->avg_humidity, 2)
            : null;

        return response()->json([
            'success' => true,
            'minutes' => $minutes,
            'avg_temperature' => $avgTemperature,
            'avg_humidity' => $avgHumidity,
        ]);
    }

    /**
     * Record one sample for the current ten-second cycle.
     *
     * Order of preference:
     *   1. A real DHT22 reading from the last 30 seconds.
     *   2. A real OpenWeather reading for Naujan, Oriental Mindoro.
     *   3. Nothing (UNAVAILABLE) - never generated or simulated values.
     *
     * This method is shared by the browser poll and the scheduler so logging
     * continues when no browser is open.
     */
    public static function sampleAndStore(): ?SensorReading
    {
        if ($recent = self::recentReading()) {
            return $recent;
        }

        // Prevent two simultaneous callers (e.g. browser + cron) from writing
        // duplicate rows for the same cycle.
        $lock = null;
        try {
            $lock = Cache::lock(self::SAMPLE_LOCK_KEY, 10);
            if (! $lock->get()) {
                // Another sampler is already working on this cycle.
                return self::recentReading();
            }
        } catch (\Throwable $e) {
            // Cache locking is not available; continue without a process lock.
            $lock = null;
        }

        try {
            if ($recent = self::recentReading()) {
                return $recent;
            }

            $hardware = self::freshHardwareReading();
            if ($hardware !== null) {
                return self::record(
                    $hardware['temperature'],
                    $hardware['humidity'],
                    self::SOURCE_HARDWARE
                );
            }

            $weather = self::fetchWeather();
            if ($weather !== null) {
                return self::record(
                    (float) $weather['temperature'],
                    (float) $weather['humidity'],
                    self::SOURCE_WEATHER
                );
            }

            return null;
        } finally {
            if ($lock !== null) {
                try {
                    $lock->release();
                } catch (\Throwable $e) {
                    // Ignore release failures; the lock will expire on its own.
                }
            }
        }
    }

    /**
     * Fetch real current weather from OpenWeather for Naujan, Oriental Mindoro.
     * Returns null when the API key is missing or the API cannot be reached.
     * No simulated fallback is ever returned.
     */
    public static function fetchWeather(bool $forceRefresh = false): ?array
    {
        if (! $forceRefresh) {
            try {
                $cached = Cache::get(self::WEATHER_CACHE_KEY);
                if (is_array($cached) && isset($cached['temperature'], $cached['humidity'], $cached['fetched_at'])) {
                    $age = now()->timestamp - (int) $cached['fetched_at'];
                    if ($age >= 0 && $age <= self::WEATHER_CACHE_SECONDS) {
                        return $cached;
                    }
                }
            } catch (\Throwable $e) {
                // Fall through to a live request.
            }
        }

        $apiKey = trim((string) env('OPENWEATHER_API_KEY', ''));
        if (
            $apiKey === ''
            || strtolower($apiKey) === 'your_api_key_here'
            || strtolower($apiKey) === 'demo'
        ) {
            return null;
        }

        try {
            // Naujan, Oriental Mindoro, Philippines (13.3233N, 121.3028E).
            $response = Http::timeout(8)->acceptJson()->get(
                'https://api.openweathermap.org/data/2.5/weather',
                [
                    'lat' => 13.3233,
                    'lon' => 121.3028,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]
            );

            if (! $response->successful()) {
                Log::warning('OpenWeather request failed', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $data = $response->json();
            if (! isset($data['main']['temp'], $data['main']['humidity'])) {
                Log::warning('OpenWeather response missing temperature or humidity');

                return null;
            }

            $result = [
                'temperature' => round((float) $data['main']['temp'], 1),
                'humidity' => round((float) $data['main']['humidity'], 1),
                'feels_like' => isset($data['main']['feels_like'])
                    ? round((float) $data['main']['feels_like'], 1)
                    : null,
                'description' => $data['weather'][0]['description'] ?? 'N/A',
                'location' => 'Naujan, Oriental Mindoro, Philippines',
                'source' => self::SOURCE_WEATHER,
                'label' => 'WEATHER',
                'fetched_at' => now()->timestamp,
                'timestamp' => now()->toIso8601String(),
            ];

            try {
                Cache::put(
                    self::WEATHER_CACHE_KEY,
                    $result,
                    now()->addSeconds(self::WEATHER_CACHE_SECONDS)
                );
            } catch (\Throwable $e) {
                Log::warning('Unable to cache OpenWeather data: '.$e->getMessage());
            }

            return $result;
        } catch (\Throwable $e) {
            Log::warning('OpenWeather request error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Convert stored/incoming source values to the two live labels.
     */
    public static function normalizeSource(?string $source): string
    {
        $source = strtolower(trim((string) $source));

        return match ($source) {
            'hardware', 'actual' => self::SOURCE_HARDWARE,
            'weather', 'openweather' => self::SOURCE_WEATHER,
            default => self::SOURCE_UNAVAILABLE,
        };
    }

    /**
     * Human-readable source label used by the API and the page.
     */
    public static function sourceLabel(?string $source): string
    {
        if (self::normalizeSource($source) === self::SOURCE_HARDWARE) {
            return 'HARDWARE';
        }

        if (self::normalizeSource($source) === self::SOURCE_WEATHER) {
            return 'WEATHER';
        }

        return 'UNAVAILABLE';
    }

    /**
     * The most recent valid row, but only if it belongs to the current cycle.
     * This suppresses duplicates from simultaneous callers while still saving
     * identical values in a later cycle.
     */
    private static function recentReading(): ?SensorReading
    {
        $latest = SensorReading::query()
            ->whereIn('source', ['hardware', 'actual', 'weather', 'openweather'])
            ->whereNotNull('temperature')
            ->whereNotNull('humidity')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if (! $latest || ! $latest->created_at) {
            return null;
        }

        return $latest->created_at->gt(now()->subSeconds(self::SAMPLE_GUARD_SECONDS))
            ? $latest
            : null;
    }

    /**
     * Return the last real hardware report while it is still within the
     * freshness window. The cache is written only by store(), so copied rows
     * cannot extend hardware freshness indefinitely.
     */
    private static function freshHardwareReading(): ?array
    {
        try {
            $state = Cache::get(self::HARDWARE_CACHE_KEY);
        } catch (\Throwable $e) {
            Log::warning('Unable to read hardware sensor cache: '.$e->getMessage());

            return null;
        }

        if (! is_array($state) || ! isset($state['temperature'], $state['humidity'], $state['at'])) {
            return null;
        }

        $age = now()->timestamp - (int) $state['at'];
        if ($age < 0 || $age > self::HARDWARE_FRESH_SECONDS) {
            return null;
        }

        return [
            'temperature' => (float) $state['temperature'],
            'humidity' => (float) $state['humidity'],
        ];
    }

    private static function rememberHardwareReading(float $temperature, float $humidity): void
    {
        try {
            Cache::put(
                self::HARDWARE_CACHE_KEY,
                [
                    'temperature' => $temperature,
                    'humidity' => $humidity,
                    'at' => now()->timestamp,
                ],
                now()->addSeconds(self::HARDWARE_FRESH_SECONDS + 5)
            );
        } catch (\Throwable $e) {
            Log::warning('Unable to cache hardware sensor state: '.$e->getMessage());
        }
    }

    private static function record(float $temperature, float $humidity, string $source): SensorReading
    {
        $reading = SensorReading::create([
            'temperature' => round($temperature, 1),
            'humidity' => round($humidity, 1),
            'source' => $source,
            'recorded_at' => now(),
        ]);

        Log::info('Sensor reading recorded', [
            'id' => $reading->id,
            'temperature' => $reading->temperature,
            'humidity' => $reading->humidity,
            'source' => $source,
        ]);

        return $reading;
    }
}
