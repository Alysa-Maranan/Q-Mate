<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'protein_percentage',
        'fat_percentage',
        'fiber_percentage',
        'suitable_breeds',
        'unsuitable_breeds',
        'health_benefits',
        'potential_risks',
        'image_url',
        'cost_per_kg',
        'is_available'
    ];

    protected $casts = [
        'protein_percentage' => 'decimal:2',
        'fat_percentage' => 'decimal:2',
        'fiber_percentage' => 'decimal:2',
        'suitable_breeds' => 'json',
        'unsuitable_breeds' => 'json',
        'cost_per_kg' => 'decimal:2',
        'is_available' => 'boolean'
    ];

    /**
     * Get all detections of this feed
     */
    public function detections(): HasMany
    {
        return $this->hasMany(FeedDetection::class);
    }

    /**
     * Check if feed is suitable for a breed
     */
    public function isSuitableForBreed($breedId)
    {
        if ($this->unsuitable_breeds && in_array($breedId, $this->unsuitable_breeds)) {
            return false;
        }
        if ($this->suitable_breeds && in_array($breedId, $this->suitable_breeds)) {
            return true;
        }
        return null; // unknown
    }

    /**
     * Get available feeds
     */
    public static function available()
    {
        return static::where('is_available', true);
    }

    /**
     * Get feeds by category
     */
    public static function byCategory($category)
    {
        return static::where('category', $category);
    }
}
