<?php

namespace Database\Seeders;

use App\Models\EggCollection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EggCollectionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with sample egg collection records.
     */
    public function run(): void
    {
        // Check if sample data already exists
        if (EggCollection::count() === 0) {
            $records = [
                [
                    'cage_pen' => 'Cage/Pen 1',
                    'total_eggs' => 28,
                    'good_eggs' => 26,
                    'cracked_eggs' => 2,
                    'collection_time' => '08:30:00',
                    'notes' => 'Morning collection - good quality eggs',
                ],
                [
                    'cage_pen' => 'Cage/Pen 2',
                    'total_eggs' => 32,
                    'good_eggs' => 30,
                    'cracked_eggs' => 2,
                    'collection_time' => '09:15:00',
                    'notes' => 'Regular morning collection',
                ],
                [
                    'cage_pen' => 'Cage/Pen 3',
                    'total_eggs' => 25,
                    'good_eggs' => 24,
                    'cracked_eggs' => 1,
                    'collection_time' => '14:30:00',
                    'notes' => 'Afternoon collection - excellent quality',
                ],
                [
                    'cage_pen' => 'Cage/Pen 4',
                    'total_eggs' => 30,
                    'good_eggs' => 27,
                    'cracked_eggs' => 3,
                    'collection_time' => '16:00:00',
                    'notes' => 'Late afternoon collection',
                ]
            ];

            foreach ($records as $record) {
                EggCollection::create($record);
            }
        }
    }
}
