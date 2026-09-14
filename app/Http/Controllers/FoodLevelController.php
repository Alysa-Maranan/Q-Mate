<?php

namespace App\Http\Controllers;

use App\Models\FoodLevel;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FoodLevelController extends Controller
{
    /**
     * API endpoint for ESP32 to update food level
     * POST /api/food-level
     */
    public function updateFromSensor(Request $request)
    {
        $validated = $request->validate([
            'distance' => ['nullable', 'integer', 'min:0', 'max:400'],
            'level'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'source'   => ['nullable', 'string', Rule::in(['ultrasonic', 'buzzer'])],
            'detected' => ['nullable', 'boolean'],
        ]);

        $source = $validated['source'] ?? null;
        $hasDetected = array_key_exists('detected', $validated);
        $distance = $validated['distance'] ?? null;
        $level = $validated['level'] ?? null;

        if ($source === 'buzzer' || ($hasDetected && $distance === null && $level === null)) {
            // Buzzer input only reports food presence, not percent.
            $foodLevel = FoodLevel::updateFromBuzzer($validated['detected'] ?? false);
        } elseif ($level !== null) {
            // Ultrasonic firmware ang nag-compute ng PERCENTAGE nang direkta.
            $foodLevel = FoodLevel::updateFromLevel($level);
        } else {
            // Backward compatibility: kung cm ang ipinadala, i-convert pa rin.
            $foodLevel = FoodLevel::updateFromSensor($distance ?? 400);
        }

        // Log the update
        Log::info('Food level updated from sensor', [
            'source' => $source,
            'distance' => $distance,
            'level_input' => $level,
            'level' => $foodLevel->level,
            'detected' => $validated['detected'] ?? null,
            'status' => $foodLevel->status,
        ]);

        // Send SMS notification if food is low or empty
        if (in_array($foodLevel->status, ['low', 'empty'])) {
            $this->sendLowFoodNotification($foodLevel);
        }

        return response()->json([
            'success' => true,
            'level' => $foodLevel->level,
            'distance' => $foodLevel->distance,
            'status' => $foodLevel->status,
            'message' => $foodLevel->getStatusMessage(),
        ]);
    }

    /**
     * Get current food level for web display
     * GET /api/food-level/current
     */
    public function getCurrent()
    {
        $foodLevel = FoodLevel::getCurrent();
        
        return response()->json([
            'level' => $foodLevel->level,
            'distance' => $foodLevel->distance,
            'status' => $foodLevel->status,
            'detected' => $foodLevel->status !== 'empty',
            'can_feed' => $foodLevel->canFeed(),
            'message' => $foodLevel->getStatusMessage(),
            'last_updated' => $foodLevel->last_updated,
        ]);
    }

    /**
     * Check if feeding is allowed
     * GET /api/food-level/can-feed
     */
    public function canFeed()
    {
        $canFeed = FoodLevel::canFeed();
        $foodLevel = FoodLevel::getCurrent();
        
        return response()->json([
            'can_feed' => $canFeed,
            'level' => $foodLevel->level,
            'status' => $foodLevel->status,
            'message' => $canFeed ? 'OK' : $foodLevel->getStatusMessage(),
        ]);
    }

    /**
     * Send SMS notification for low food
     */
    private function sendLowFoodNotification(FoodLevel $foodLevel)
    {
        $phoneNumber = env('SMS_PHONE_NUMBER');
        
        if (!$phoneNumber) {
            Log::warning('SMS_PHONE_NUMBER not set, skipping notification');
            return;
        }

        try {
            $twilio = new TwilioService();
            $message = "🚨 SQUIFM ALERT: {$foodLevel->getStatusMessage()} Current level: {$foodLevel->level}%";
            
            $result = $twilio->sendSMS($phoneNumber, $message);

            if ($result['success']) {
                Log::info('Low food SMS sent successfully');
            } else {
                Log::error('Failed to send low food SMS: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Exception sending low food SMS: ' . $e->getMessage());
        }
    }
}
