<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedHistory;
use App\Services\TwilioService;
use Carbon\Carbon;

class FeederAuto extends Command
{
    protected $signature = 'feeder:auto';
    protected $description = 'Automatically trigger feeding at 8AM, 12PM, 5PM for 20 seconds';

    const DURATION = 20;

    public function handle()
    {
        $port   = env('SERVO_SERIAL_PORT', 'COM3');
        $now    = Carbon::now();

        $this->info('Automatic feeding triggered at ' . $now->format('h:i A'));

        // Write servo command for servo_bridge.py to pick up
        file_put_contents(storage_path('logs/servo_command.json'), json_encode([
            'action'   => 'trigger',
            'duration' => self::DURATION,
            'port'     => $port,
            'cage'     => 1,
            'type'     => 'scheduled', // This will create animation
        ]));

        $this->info('Servo command sent. Waiting for completion...');

        // Wait for feeding to finish
        sleep(self::DURATION + 3);

        // Record to feed history
        FeedHistory::create([
            'feed_type'   => 'automatic',
            'feed_brand'  => cache('feed_brand', 'Quail Layer Smash'),
            'duration'    => self::DURATION,
            'cage_number' => 1,
            'status'      => 'completed',
            'fed_at'      => $now,
        ]);

        $this->sendSms('Automatic feeding completed at ' . $now->format('h:i A'));
        $this->info('Feeding completed and logged.');

        return 0;
    }

    private function sendSms($message)
    {
        $phone = env('SMS_PHONE_NUMBER');
        if (!$phone) return;
        try {
            $twilio = new TwilioService();
            $twilio->sendSMS($phone, $message);
        } catch (\Exception $e) {
            $this->error('SMS failed: ' . $e->getMessage());
        }
    }
}
