<?php
// Quick script to verify and reseed breeds
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\QuailBreed;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear existing breeds
DB::table('quail_breeds')->truncate();

// Seed fresh data
$breeds = [
    [
        'name' => 'Japanese Quail (Coturnix Japonica)',
        'scientific_name' => 'Coturnix japonica',
        'description' => 'Most common commercial quail breed. Hardy, fast-growing, excellent egg production.',
        'egg_production_rate' => 280,
        'mature_weight' => 120.00,
        'maturity_age' => 42,
        'is_active' => true,
        'color_markings' => 'Brown with black markings',
        'size_category' => 'Small',
        'optimal_temperature' => 20.5,
        'optimal_humidity' => 57.5,
        'care_requirements' => 'Needs well-ventilated housing, regular feeding and water',
        'recommended_feeds' => json_encode(['Premium Pellets', 'Corn', 'Millet']),
        'common_diseases' => json_encode(['Aspergillosis', 'Coccidiosis', 'Bacterial Infection']),
        'image_url' => null
    ],
    [
        'name' => 'Japanese Coturnix Crossbreed (Taiwan Brown Line)',
        'scientific_name' => 'Coturnix japonica crossbreed',
        'description' => 'Hybrid variety from Taiwan, excellent egg production with good meat quality.',
        'egg_production_rate' => 300,
        'mature_weight' => 135.00,
        'maturity_age' => 40,
        'is_active' => true,
        'color_markings' => 'Brown with lighter chest',
        'size_category' => 'Small',
        'optimal_temperature' => 20.5,
        'optimal_humidity' => 57.5,
        'care_requirements' => 'Similar to Japanese Quail, good ventilation required',
        'recommended_feeds' => json_encode(['Premium Pellets', 'Corn', 'Millet']),
        'common_diseases' => json_encode(['Aspergillosis', 'Coccidiosis']),
        'image_url' => null
    ],
    [
        'name' => 'Pharaoh Quail',
        'scientific_name' => 'Coturnix coturnix pharaoh',
        'description' => 'Brown-colored strain of Japanese quail. Good for both meat and egg production.',
        'egg_production_rate' => 260,
        'mature_weight' => 130.00,
        'maturity_age' => 45,
        'is_active' => true,
        'color_markings' => 'Dark brown plumage',
        'size_category' => 'Small',
        'optimal_temperature' => 20.5,
        'optimal_humidity' => 57.5,
        'care_requirements' => 'Similar to Japanese Quail',
        'recommended_feeds' => json_encode(['Premium Pellets', 'Corn']),
        'common_diseases' => json_encode(['Aspergillosis', 'Coccidiosis']),
        'image_url' => null
    ],
];

foreach ($breeds as $breed) {
    QuailBreed::create($breed);
}

echo "✓ Seeded 3 quail breeds successfully!\n";
$count = QuailBreed::count();
echo "✓ Total breeds in database: " . $count . "\n";

foreach (QuailBreed::all() as $breed) {
    echo "  - {$breed->name}\n";
}
