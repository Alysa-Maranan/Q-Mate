<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'quail_breed_id',
        'detection_type',
        'confidence',
        'image_path',
        'detection_notes',
        'location',
        'raw_detection_data'
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'raw_detection_data' => 'json'
    ];

    /**
     * Get the user who made the detection
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quail breed detected
     */
    public function breed(): BelongsTo
    {
        return $this->belongsTo(QuailBreed::class, 'quail_breed_id');
    }

    /**
     * Get detections by type
     */
    public static function byType($type)
    {
        return static::where('detection_type', $type);
    }

    /**
     * Get successful detections (quail)
     */
    public static function successful()
    {
        return static::where('detection_type', 'quail');
    }

    /**
     * Get failed detections (human/unknown)
     */
    public static function failed()
    {
        return static::whereIn('detection_type', ['human', 'unknown']);
    }

    /**
     * Get high confidence detections
     */
    public static function highConfidence($threshold = 0.8)
    {
        return static::where('confidence', '>=', $threshold);
    }

    /**
     * Get detections for today
     */
    public static function today()
    {
        return static::whereDate('created_at', today());
    }
}
