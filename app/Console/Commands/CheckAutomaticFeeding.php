<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FarmSetting;
use App\Models\FeedHistory;
use Illuminate\Support\Facades\Log;

class CheckAutomaticFeeding extends Command
{
    protected $signature = 'feeder:check-automatic';
    protected $description = 'Check if it\'s time for automatic feeding (reads from settings)';

    public function handle()
    {
        $now = now();
        $currentTime = $now->format('H:i');
        
        // Get feeding times from settings (DYNAMIC - reads every time)
        $morningTime = FarmSetting::get('feed_time_morning', '08:00');
        $afternoonTime = FarmSetting::get('feed_time_afternoon', '12:00');
        $eveningTime = FarmSetting::get('feed_time_evening', '17:00');
        
        $feedingTimes = [$morningTime, $afternoonTime, $eveningTime];
        
        // Check if current time matches any feeding time (EXACT match)
        if (in_array($currentTime, $feedingTimes)) {
            // Check if we already fed at this time (prevent duplicate within same minute)
            $lastFeedFile = storage_path('logs/last_auto_feed.txt');
            $lastFeedTime = file_exists($lastFeedFile) ? file_get_contents($lastFeedFile) : '';
            
            if ($lastFeedTime === $currentTime) {
                return 0; // Already fed this minute
            }
            
            Log::info("EXACT TIME MATCH! Triggering feeding NOW at $currentTime");
            
            // Trigger feeding via servo_bridge.py IMMEDIATELY
            $cmdFile = storage_path('logs/servo_command.json');
            $port = env('SERVO_SERIAL_PORT', 'COM3');
            
            file_put_contents($cmdFile, json_encode([
                'action'   => 'trigger',
                'duration' => 300,
                'port'     => $port,
                'cage'     => 1,
                'type'     => 'scheduled', // Will create animation
            ]));
            Log::info("Scheduled feeding function triggered; servo command sent", [
                'time' => $currentTime,
                'port' => $port,
            ]);
            
            // Save last feed time to prevent duplicate
            file_put_contents($lastFeedFile, $currentTime);
            
            Log::info("Automatic feeding triggered IMMEDIATELY at $currentTime");
            
            // Wait for feeding to complete
            sleep(23);
            
            // Record to feed history
            FeedHistory::create([
                'feed_type' => 'automatic',
                'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                'duration' => 300,
                'cage_number' => 1,
                'status' => 'completed',
                'fed_at' => $now,
            ]);
            
            Log::info("Automatic feeding completed at $currentTime");
        }
        
        return 0;
    }
}
