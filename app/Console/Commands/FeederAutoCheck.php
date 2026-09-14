<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FeederAutoCheck extends Command
{
    protected $signature = 'feeder:auto-check';
    protected $description = 'Automatically check and trigger feeding schedules';

    public function handle()
    {
        try {
            // Call the auto-check endpoint
            $response = Http::timeout(5)->get(url('/feeder/schedules/auto-check'));
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['count'] > 0) {
                    $this->info('Triggered ' . $data['count'] . ' feeding schedule(s)');
                }
            }
        } catch (\Exception $e) {
            $this->error('Auto-check failed: ' . $e->getMessage());
        }
        
        return 0;
    }
}
