<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorReading;
use Carbon\Carbon;

class SensorReadingsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        SensorReading::truncate();

        $now = Carbon::now();
        $readings = [];

        // Generate 48 hours of sample data (every 30 minutes)
        for ($i = 0; $i < 96; $i++) {
            $time = $now->copy()->subMinutes($i * 30);
            
            // Simulate realistic temperature and humidity for quail farm
            // Temperature: 22-28°C (optimal for quails)
            // Humidity: 55-75% (good for quails)
            
            // Add some variation based on time of day
            $hour = $time->hour;
            $baseTemp = 25; // Base temperature
            $baseHumidity = 65; // Base humidity
            
            // Temperature variations (cooler at night, warmer during day)
            if ($hour >= 6 && $hour <= 18) {
                // Daytime: slightly warmer
                $tempVariation = rand(-2, 3);
            } else {
                // Nighttime: slightly cooler
                $tempVariation = rand(-3, 1);
            }
            
            // Humidity variations (higher at night, lower during day)
            if ($hour >= 6 && $hour <= 18) {
                // Daytime: lower humidity
                $humidityVariation = rand(-10, 5);
            } else {
                // Nighttime: higher humidity
                $humidityVariation = rand(-5, 10);
            }
            
            $temperature = $baseTemp + $tempVariation + (rand(-10, 10) / 10);
            $humidity = $baseHumidity + $humidityVariation + (rand(-50, 50) / 10);
            
            // Keep within realistic bounds
            $temperature = max(18, min(32, $temperature));
            $humidity = max(40, min(85, $humidity));
            
            $readings[] = [
                'temperature' => round($temperature, 1),
                'humidity' => round($humidity, 1),
                'recorded_at' => $time,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        // Insert all readings
        SensorReading::insert($readings);
        
        $this->command->info('Created ' . count($readings) . ' sensor readings');
    }
}