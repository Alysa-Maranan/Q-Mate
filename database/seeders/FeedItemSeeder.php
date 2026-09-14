<?php

namespace Database\Seeders;

use App\Models\FeedItem;
use Illuminate\Database\Seeder;

class FeedItemSeeder extends Seeder
{
    public function run()
    {
        $feedItems = [
            [
                'name' => 'Premium Quail Pellets',
                'description' => 'Complete nutrition formulated specifically for quail',
                'category' => 'pellets',
                'protein_percentage' => 24.0,
                'fat_percentage' => 5.0,
                'fiber_percentage' => 4.0,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'Optimal nutrition for egg production and growth',
                'cost_per_kg' => 180.00,
                'is_available' => true,
            ],
            [
                'name' => 'Cracked Corn',
                'description' => 'Energy supplement for quails',
                'category' => 'grains',
                'protein_percentage' => 8.5,
                'fat_percentage' => 3.5,
                'fiber_percentage' => 2.0,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'Good energy source, can be used as treat',
                'potential_risks' => 'Do not exceed 20% of daily feed',
                'cost_per_kg' => 45.00,
                'is_available' => true,
            ],
            [
                'name' => 'Millet Seeds',
                'description' => 'Natural grain treats for quails',
                'category' => 'grains',
                'protein_percentage' => 12.3,
                'fat_percentage' => 3.8,
                'fiber_percentage' => 2.1,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'Nutritious treats, promotes natural foraging',
                'cost_per_kg' => 60.00,
                'is_available' => true,
            ],
            [
                'name' => 'Vegetable Mix',
                'description' => 'Fresh vegetables for balanced diet',
                'category' => 'vegetables',
                'protein_percentage' => 2.5,
                'fat_percentage' => 0.3,
                'fiber_percentage' => 2.8,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'Vitamins and minerals, promotes health',
                'cost_per_kg' => 50.00,
                'is_available' => true,
            ],
            [
                'name' => 'Oyster Shell Grit',
                'description' => 'Calcium supplement for egg-laying quails',
                'category' => 'supplements',
                'protein_percentage' => 0.0,
                'fat_percentage' => 0.0,
                'fiber_percentage' => 0.0,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'Essential for strong eggshells',
                'cost_per_kg' => 80.00,
                'is_available' => true,
            ],
            [
                'name' => 'Fish Meal',
                'description' => 'Protein-rich supplement',
                'category' => 'supplements',
                'protein_percentage' => 60.0,
                'fat_percentage' => 8.0,
                'fiber_percentage' => 1.0,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'High protein for growth and egg production',
                'potential_risks' => 'Use in moderation, maximum 5% of feed',
                'cost_per_kg' => 120.00,
                'is_available' => true,
            ],
            [
                'name' => 'Spinach Leaves',
                'description' => 'Leafy green vegetable',
                'category' => 'vegetables',
                'protein_percentage' => 2.7,
                'fat_percentage' => 0.4,
                'fiber_percentage' => 0.7,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'Rich in vitamins and minerals',
                'cost_per_kg' => 60.00,
                'is_available' => true,
            ],
            [
                'name' => 'Mealworms (Dried)',
                'description' => 'Protein-packed insect treats',
                'category' => 'supplements',
                'protein_percentage' => 75.0,
                'fat_percentage' => 5.0,
                'fiber_percentage' => 2.0,
                'suitable_breeds' => [1, 2],
                'health_benefits' => 'High protein, natural treats loved by quails',
                'potential_risks' => 'Use occasionally as treats only',
                'cost_per_kg' => 250.00,
                'is_available' => true,
            ],
        ];

        foreach ($feedItems as $item) {
            FeedItem::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
