<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLevel extends Model
{
    use HasFactory;

    // =================================================
    // CALIBRATION - IMPORTANTE! I-ADJUST MO ITO:
    // =================================================
    // Tingnan mo ang ARDUINO Serial Monitor at itala ang
    // "Distance: X cm" sa dalawang sitwasyon:
    //
    //  1. PUNO ang lalyan (full)   -> tandaan ang reading ng sensor
    //  2. WALANG LAMAN (empty)     -> tandaan ang reading ng sensor
    //
    // Halimbawa: kung full = 5cm, empty = 45cm:
    const FULL_DISTANCE_CM  = 5;   // <-- DISTANCE kapag PUNO (may pagkain)
    const EMPTY_DISTANCE_CM = 45;  // <-- DISTANCE kapag WALANG LAMAN (ibaba)

    protected $fillable = [
        'level',
        'distance',
        'status',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    /**
     * Get the current food level (singleton pattern - only one record)
     */
    public static function getCurrent(): self
    {
        $record = self::first();
        if (!$record) {
            // Return empty record without saving to database
            return new self([
                'level' => 0,
                'status' => 'empty',
                'last_updated' => null,
            ]);
        }
        return $record;
    }

    /**
     * Update food level from sensor
     */
    public static function updateFromSensor(int $distance): self
    {
        // Convert distance to percentage using calibration constants.
        // PUNO (full)    = small distance  -> 100%
        // WALANG LAMAN   = large distance  -> 0%
        $full  = self::FULL_DISTANCE_CM;
        $empty = self::EMPTY_DISTANCE_CM;

        // Guard against invalid calibration
        if ($empty <= $full) {
            $empty = $full + 1;
        }

        $level = 0;
        if ($distance <= $full) {
            $level = 100;
        } elseif ($distance >= $empty) {
            $level = 0;
        } else {
            $level = (int)round((($empty - $distance) / ($empty - $full)) * 100);
        }

        $level = max(0, min(100, $level));

        $status = 'normal';
        if ($level <= 0) {
            $status = 'empty';
        } elseif ($level <= 10) {
            $status = 'low';
        }

        $foodLevel = self::getCurrent();
        $data = [
            'level' => $level,
            'distance' => $distance,
            'status' => $status,
            'last_updated' => now(),
        ];

        if ($foodLevel->exists) {
            $foodLevel->update($data);
        } else {
            $foodLevel = self::create($data);
        }

        return $foodLevel;
    }

    /**
     * Update food level direkta mula sa ultrasonic firmware (PERCENTAGE).
     * Ang IDE/firmware na mismo ang nagde-decide kung ilang % ang laman.
     * Kapag walang nadedetect ang sensor -> nagpapadala ng 0%.
     */
    public static function updateFromLevel(int $level): self
    {
        $level = max(0, min(100, $level));

        $status = 'normal';
        if ($level <= 0) {
            $status = 'empty';
        } elseif ($level <= 10) {
            $status = 'low';
        }

        $foodLevel = self::getCurrent();
        $data = [
            'level' => $level,
            'distance' => null,
            'status' => $status,
            'last_updated' => now(),
        ];

        if ($foodLevel->exists) {
            $foodLevel->update($data);
        } else {
            $foodLevel = self::create($data);
        }

        return $foodLevel;
    }

    /**
     * Update food presence from buzzer detection.
     * Buzzer input does not change the percentage level.
     */
    public static function updateFromBuzzer(bool $detected): self
    {
        $foodLevel = self::getCurrent();

        $status = $detected ? 'normal' : 'empty';
        $data = [
            'status' => $status,
            'last_updated' => now(),
        ];

        if ($foodLevel->exists) {
            $foodLevel->update($data);
        } else {
            $foodLevel = self::create($data);
        }

        return $foodLevel;
    }

    /**
     * Check if food is available for feeding
     */
    public static function canFeed(): bool
    {
        $current = self::getCurrent();
        return $current->level > 10; // Can feed if more than 10%
    }

    /**
     * Get status message
     */
    public function getStatusMessage(): string
    {
        return match($this->status) {
            'empty' => 'Food container is empty! Please refill the food container.',
            'low' => 'Food level is low! Please refill the food container.',
            default => 'Sufficient food available.',
        };
    }

}
