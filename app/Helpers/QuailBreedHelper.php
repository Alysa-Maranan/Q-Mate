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

    public static function getCurrentBreeds()
    {
        $breed = self::getCurrentBreed();

        return $breed ? [$breed] : [];
    }

    public static function setCurrentBreeds($breedId1, $breedId2 = null)
    {
        self::setCurrentBreed($breedId1);

        FarmSetting::set(
            'current_breed_ids',
            json_encode([$breedId1])
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