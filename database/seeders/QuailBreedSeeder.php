<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\QuailBreed;

class QuailBreedSeeder extends Seeder
{
    public function run()
    {
        DB::table('quail_breeds')->delete();

        QuailBreed::create([
            "name" => "Japanese Quail / Coturnix Quail",
            "scientific_name" => "Coturnix japonica",
            "description" => "Most common.",
            "egg_production_rate" => 280,
            "mature_weight" => 120,
            "maturity_age" => 42,
            "is_active" => true,
            "color_markings" => "Brown",
            "size_category" => "Small",
            "optimal_temperature" => 20.5,
            "optimal_humidity" => 57.5,
            "care_requirements" => "Housing",
            "recommended_feeds" => json_encode(["Pellets"]),
            "common_diseases" => json_encode(["Aspergillosis"]),
            "image_url" => null
        ]);

        QuailBreed::create([
            "name" => "Japanese Coturnix Crossbreed (Taiwan Brown Line)",
            "scientific_name" => "Coturnix japonica (Taiwan)",
            "description" => "Enhanced strain from Taiwan.",
            "egg_production_rate" => 300,
            "mature_weight" => 135,
            "maturity_age" => 40,
            "is_active" => true,
            "color_markings" => "Brown",
            "size_category" => "Small",
            "optimal_temperature" => 20.5,
            "optimal_humidity" => 57.5,
            "care_requirements" => "Housing",
            "recommended_feeds" => json_encode(["Pellets"]),
            "common_diseases" => json_encode(["Aspergillosis"]),
            "image_url" => null
        ]);

        QuailBreed::create([
            "name" => "Pharaoh Quail",
            "scientific_name" => "Coturnix coturnix pharaoh",
            "description" => "Brown strain.",
            "egg_production_rate" => 260,
            "mature_weight" => 130,
            "maturity_age" => 45,
            "is_active" => true,
            "color_markings" => "Dark brown",
            "size_category" => "Small",
            "optimal_temperature" => 20.5,
            "optimal_humidity" => 57.5,
            "care_requirements" => "Standard",
            "recommended_feeds" => json_encode(["Pellets"]),
            "common_diseases" => json_encode(["Coccidiosis"]),
            "image_url" => null
        ]);
    }
}