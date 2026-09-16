<?php

namespace App\Helpers;

use App\Models\QuailBreed;
use App\Models\FarmSetting;

class QuailBreedHelper
{
    public static function getCurrentBreed()
{
    $breedId = FarmSetting::get('current_breed_id', '1');

    $breed = QuailBreed::find($breedId);

    if ($breed) {
        return $breed;
    }

    return QuailBreed::first();
}

    public static function setCurrentBreed($breedId)
    {
        FarmSetting::set('current_breed_id', $breedId);
    }

    // Get both selected breeds
    public static function getCurrentBreeds()
    {
        $breedIds = json_decode(
            FarmSetting::get(
                'current_breed_ids',
                json_encode([1, 1])
            ),
            true
        );

        // Make sure the setting contains a valid array
        if (!is_array($breedIds)) {
            $breedIds = [1, 1];
        }

        $breeds = [];

        foreach ($breedIds as $breedId) {
            if (!$breedId) {
                continue;
            }

            $breed = QuailBreed::find($breedId);

            if ($breed) {
                $breeds[] = $breed;
            }
        }

        // Remove duplicate breeds while preserving the selected order
        $breeds = collect($breeds)
            ->unique('id')
            ->values()
            ->all();

        // If no valid breeds are found, use the first available breed
        if (empty($breeds)) {
            $default = QuailBreed::first();

            return $default ? [$default] : [];
        }

        return $breeds;
    }

    // Set both selected breeds
    public static function setCurrentBreeds($breedId1, $breedId2)
    {
        $breedIds = [$breedId1, $breedId2];

        FarmSetting::set(
            'current_breed_ids',
            json_encode($breedIds)
        );
    }

    public static function getBreedInfo($field = null)
    {
        $breed = self::getCurrentBreed();

        if (!$breed) {
            return $field ? null : [];
        }

        $info = [
            'name' => $breed->name,
            'scientific_name' => $breed->scientific_name,
            'description' => $breed->description,
            'egg_production_rate' => $breed->egg_production_rate,
            'mature_weight' => $breed->mature_weight,
            'maturity_age' => $breed->maturity_age
        ];

        return $field ? ($info[$field] ?? null) : $info;
    }
}