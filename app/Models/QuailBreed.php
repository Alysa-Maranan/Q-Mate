<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuailBreed extends Model
{
    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'egg_production_rate',
        'mature_weight',
        'maturity_age',
        'color_markings',
        'size_category',
        'optimal_temperature',
        'optimal_humidity',
        'care_requirements',
        'recommended_feeds',
        'common_diseases',
        'image_url',
        'is_active'
    ];

    protected $casts = [
        'egg_production_rate' => 'integer',
        'mature_weight' => 'decimal:2',
        'maturity_age' => 'integer',
        'optimal_temperature' => 'decimal:1',
        'optimal_humidity' => 'decimal:1',
        'recommended_feeds' => 'json',
        'common_diseases' => 'json',
        'is_active' => 'boolean'
    ];

    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('name')->get();
    }

    public static function getCurrent()
    {
        $currentBreedId = FarmSetting::get('current_breed_id', '1');
        return static::find($currentBreedId) ?? static::first();
    }
}