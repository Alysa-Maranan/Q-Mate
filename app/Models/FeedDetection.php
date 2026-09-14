<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedDetection extends Model
{
    protected $fillable = [
        'user_id',
        'quail_breed_id',
        'feed_item_id',
        'feed_name',
        'confidence',
        'compatibility_status',
        'compatibility_notes',
        'image_path',
        'detected_attributes'
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'detected_attributes' => 'json'
    ];

    /**
     * Get the user who detected the feed
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quail breed
     */
    public function breed(): BelongsTo
    {
        return $this->belongsTo(QuailBreed::class, 'quail_breed_id');
    }

    /**
     * Get the detected feed item
     */
    public function feedItem(): BelongsTo
    {
        return $this->belongsTo(FeedItem::class);
    }

    /**
     * Get compatible detections
     */
    public static function compatible()
    {
        return static::where('compatibility_status', 'suitable');
    }

    /**
     * Get incompatible detections
     */
    public static function incompatible()
    {
        return static::whereIn('compatibility_status', ['unsuitable']);
    }

    /**
     * Get caution detections
     */
    public static function caution()
    {
        return static::where('compatibility_status', 'caution');
    }

    /**
     * Get detections for today
     */
    public static function today()
    {
        return static::whereDate('created_at', today());
    }
}
