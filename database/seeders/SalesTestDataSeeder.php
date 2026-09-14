<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\EggCollection;
use Carbon\Carbon;

class SalesTestDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample sales for the last 30 days
        $sales = [
            [
                'product_type' => 'eggs',
                'quantity' => 5,
                'price' => 80.00,
                'total' => 400.00,
                'customer_name' => 'Juan Dela Cruz',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'product_type' => 'live_quail',
                'quantity' => 3,
                'price' => 180.00,
                'total' => 540.00,
                'customer_name' => 'Maria Santos',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'product_type' => 'dressed_quail',
                'quantity' => 2,
                'price' => 250.00,
                'total' => 500.00,
                'customer_name' => 'Pedro Garcia',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'product_type' => 'eggs',
                'quantity' => 8,
                'price' => 80.00,
                'total' => 640.00,
                'customer_name' => 'Ana Reyes',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'product_type' => 'live_quail',
                'quantity' => 6,
                'price' => 180.00,
                'total' => 1080.00,
                'customer_name' => 'Carlos Lopez',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'product_type' => 'eggs',
                'quantity' => 4,
                'price' => 80.00,
                'total' => 320.00,
                'customer_name' => 'Lisa Tan',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'product_type' => 'dressed_quail',
                'quantity' => 3,
                'price' => 250.00,
                'total' => 750.00,
                'customer_name' => 'Roberto Cruz',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'product_type' => 'eggs',
                'quantity' => 6,
                'price' => 80.00,
                'total' => 480.00,
                'customer_name' => 'Elena Morales',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
        ];

        foreach ($sales as $sale) {
            Sale::create($sale);
        }

        // Create sample egg collections
        $collections = [
            [
                'cage_pen' => '1',
                'total_eggs' => 25,
                'good_eggs' => 23,
                'cracked_eggs' => 2,
                'collection_time' => '07:30:00',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'cage_pen' => '2',
                'total_eggs' => 28,
                'good_eggs' => 26,
                'cracked_eggs' => 2,
                'collection_time' => '08:00:00',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'cage_pen' => '1',
                'total_eggs' => 22,
                'good_eggs' => 20,
                'cracked_eggs' => 2,
                'collection_time' => '07:45:00',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'cage_pen' => '3',
                'total_eggs' => 30,
                'good_eggs' => 28,
                'cracked_eggs' => 2,
                'collection_time' => '08:15:00',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'cage_pen' => '2',
                'total_eggs' => 26,
                'good_eggs' => 24,
                'cracked_eggs' => 2,
                'collection_time' => '07:30:00',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'cage_pen' => '4',
                'total_eggs' => 24,
                'good_eggs' => 22,
                'cracked_eggs' => 2,
                'collection_time' => '08:00:00',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($collections as $collection) {
            EggCollection::create($collection);
        }

        $this->command->info('Sample sales and egg collection data created successfully!');
        $this->command->info('Created ' . count($sales) . ' sample sales');
        $this->command->info('Created ' . count($collections) . ' sample egg collections');
        $this->command->info('Total revenue: ₱' . number_format(array_sum(array_column($sales, 'total')), 2));
    }
}