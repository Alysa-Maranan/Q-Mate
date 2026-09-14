<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedingSchedule;
use App\Models\FarmSetting;

class SetupDailyFeeding extends Command
{
    protected $signature = 'feeder:setup-daily';
    protected $description = 'Setup daily feeding schedules (morning, afternoon, evening)';

    public function handle()
    {
        $morningTime = FarmSetting::get('feed_time_morning', '08:00');
        $afternoonTime = FarmSetting::get('feed_time_afternoon', '12:00');
        $eveningTime = FarmSetting::get('feed_time_evening', '17:00');

        $feedingTimes = [
            ['time' => $morningTime, 'label' => 'Morning'],
            ['time' => $afternoonTime, 'label' => 'Afternoon'],
            ['time' => $eveningTime, 'label' => 'Evening'],
        ];

        $this->info('Setting up daily feeding schedules...');

        foreach ($feedingTimes as $feeding) {
            $time = $feeding['time'] . ':00'; // Add seconds
            
            // Check if schedule already exists
            $existing = FeedingSchedule::where('time', $time)
                ->where('cage_number', 1)
                ->first();

            if ($existing) {
                $this->info("✓ {$feeding['label']} feeding at {$feeding['time']} already exists");
                continue;
            }

            // Create new schedule
            FeedingSchedule::create([
                'time' => $time,
                'days' => [], // Empty array means every day
                'amount' => 300, // 300 seconds = 5 minutes
                'cage_number' => 1,
                'enabled' => true,
                'status' => 'idle',
            ]);

            $this->info("✓ Created {$feeding['label']} feeding at {$feeding['time']}");
        }

        $this->info('');
        $this->info('Daily feeding schedules setup complete!');
        $this->info('Schedules will run automatically at:');
        $this->info("  🌅 Morning:   {$morningTime}");
        $this->info("  ☀️  Afternoon: {$afternoonTime}");
        $this->info("  🌇 Evening:   {$eveningTime}");

        return 0;
    }
}
