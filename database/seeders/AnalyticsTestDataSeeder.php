<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactMessage;
use App\Models\FeedHistory;
use Carbon\Carbon;

class AnalyticsTestDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample orders for the last 30 days
        $orders = [
            [
                'fullname' => 'Juan Dela Cruz',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: quail_eggs x5',
                'message' => "Name: Juan Dela Cruz\nContact: 09171234567\nLocation: Quezon City\nProduct: quail_eggs\nQty: 5\nDate: " . Carbon::now()->subDays(2)->format('Y-m-d') . "\nOrder Type: pickup\nAddress: \nNotes: ",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'fullname' => 'Maria Santos',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: live_quail x3',
                'message' => "Name: Maria Santos\nContact: 09181234567\nLocation: Manila\nProduct: live_quail\nQty: 3\nDate: " . Carbon::now()->subDays(5)->format('Y-m-d') . "\nOrder Type: delivery\nAddress: 123 Main St, Manila\nNotes: Please deliver in the morning",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'fullname' => 'Pedro Garcia',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: dressed_quail x2',
                'message' => "Name: Pedro Garcia\nContact: 09191234567\nLocation: Makati\nProduct: dressed_quail\nQty: 2\nDate: " . Carbon::now()->subDays(7)->format('Y-m-d') . "\nOrder Type: pickup\nAddress: \nNotes: ",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'fullname' => 'Ana Reyes',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: quail_eggs x10',
                'message' => "Name: Ana Reyes\nContact: 09201234567\nLocation: Pasig\nProduct: quail_eggs\nQty: 10\nDate: " . Carbon::now()->subDays(10)->format('Y-m-d') . "\nOrder Type: delivery\nAddress: 456 Oak Ave, Pasig\nNotes: ",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'fullname' => 'Carlos Lopez',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: live_quail x5',
                'message' => "Name: Carlos Lopez\nContact: 09211234567\nLocation: Taguig\nProduct: live_quail\nQty: 5\nDate: " . Carbon::now()->subDays(12)->format('Y-m-d') . "\nOrder Type: pickup\nAddress: \nNotes: For breeding",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'fullname' => 'Lisa Tan',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: quail_eggs x7',
                'message' => "Name: Lisa Tan\nContact: 09221234567\nLocation: Mandaluyong\nProduct: quail_eggs\nQty: 7\nDate: " . Carbon::now()->subDays(15)->format('Y-m-d') . "\nOrder Type: delivery\nAddress: 789 Pine St, Mandaluyong\nNotes: ",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'fullname' => 'Roberto Cruz',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: dressed_quail x4',
                'message' => "Name: Roberto Cruz\nContact: 09231234567\nLocation: San Juan\nProduct: dressed_quail\nQty: 4\nDate: " . Carbon::now()->subDays(18)->format('Y-m-d') . "\nOrder Type: pickup\nAddress: \nNotes: For restaurant",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'fullname' => 'Elena Morales',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: quail_eggs x3',
                'message' => "Name: Elena Morales\nContact: 09241234567\nLocation: Pasay\nProduct: quail_eggs\nQty: 3\nDate: " . Carbon::now()->subDays(20)->format('Y-m-d') . "\nOrder Type: delivery\nAddress: 321 Elm St, Pasay\nNotes: ",
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'fullname' => 'Miguel Rivera',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: live_quail x8',
                'message' => "Name: Miguel Rivera\nContact: 09251234567\nLocation: Muntinlupa\nProduct: live_quail\nQty: 8\nDate: " . Carbon::now()->subDays(25)->format('Y-m-d') . "\nOrder Type: pickup\nAddress: \nNotes: Large order for farm",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'fullname' => 'Sofia Hernandez',
                'email' => 'order@escalona-farm.local',
                'subject' => 'ORDER: dressed_quail x6',
                'message' => "Name: Sofia Hernandez\nContact: 09261234567\nLocation: Paranaque\nProduct: dressed_quail\nQty: 6\nDate: " . Carbon::now()->subDays(28)->format('Y-m-d') . "\nOrder Type: delivery\nAddress: 654 Maple Ave, Paranaque\nNotes: Special occasion",
                'status' => 'done',
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(28),
            ],
        ];

        foreach ($orders as $order) {
            ContactMessage::create($order);
        }

        // Create sample feeding history
        $feedingHistory = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Add 2-4 scheduled feedings per day
            $scheduledFeedings = rand(2, 4);
            for ($j = 0; $j < $scheduledFeedings; $j++) {
                $feedingHistory[] = [
                    'feed_type' => 'scheduled',
                    'schedule_id' => null,
                    'manual_feed_id' => null,
                    'duration' => rand(3, 8),
                    'days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                    'status' => 'completed',
                    'fed_at' => $date->copy()->addHours(rand(6, 18)),
                    'created_at' => $date->copy()->addHours(rand(6, 18)),
                    'updated_at' => $date->copy()->addHours(rand(6, 18)),
                ];
            }
            
            // Add 0-2 manual feedings per day (less frequent)
            $manualFeedings = rand(0, 2);
            for ($j = 0; $j < $manualFeedings; $j++) {
                $feedingHistory[] = [
                    'feed_type' => 'manual',
                    'schedule_id' => null,
                    'manual_feed_id' => null,
                    'duration' => rand(2, 5),
                    'days' => json_encode([]),
                    'status' => 'completed',
                    'fed_at' => $date->copy()->addHours(rand(8, 20)),
                    'created_at' => $date->copy()->addHours(rand(8, 20)),
                    'updated_at' => $date->copy()->addHours(rand(8, 20)),
                ];
            }
        }

        foreach ($feedingHistory as $feeding) {
            FeedHistory::create($feeding);
        }

        $this->command->info('Sample analytics data created successfully!');
        $this->command->info('Created ' . count($orders) . ' sample orders');
        $this->command->info('Created ' . count($feedingHistory) . ' feeding history records');
    }
}