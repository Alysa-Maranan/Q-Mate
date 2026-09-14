<?php

namespace App\Http\Controllers;

use App\Models\FeederCommand;
use App\Models\FeedingSchedule;
use App\Models\ManualFeed;
use App\Models\FeedHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeederCommandController extends Controller
{
    /**
     * ESP32 polls this endpoint every 2 seconds to check for pending feed commands
     * GET /api/feeder/pending-command
     * 
     * Returns:
     *   { "has_command": true, "command_id": 5, "duration": 10, "type": "scheduled" }
     *   or
     *   { "has_command": false }
     */
    public function getPendingCommand()
    {
        // Clean up stale commands first (older than 5 minutes)
        FeederCommand::cleanupStale();

        // Get the oldest pending command
        $command = FeederCommand::getNextPending();

        if (!$command) {
            return response()->json([
                'has_command' => false,
            ]);
        }

        // Mark as picked up so it won't be returned again
        $command->markPickedUp();

        Log::info('ESP32 picked up feeder command', [
            'command_id' => $command->id,
            'type' => $command->type,
            'duration' => $command->duration,
        ]);

        return response()->json([
            'has_command' => true,
            'command_id' => $command->id,
            'duration' => $command->duration,
            'type' => $command->type,
        ]);
    }

    /**
     * ESP32 calls this when feeding is done
     * POST /api/feeder/command-done
     * Body: { "command_id": 5 }
     */
    public function commandDone(Request $request)
    {
        $validated = $request->validate([
            'command_id' => 'required|integer|exists:feeder_commands,id',
        ]);

        $command = FeederCommand::findOrFail($validated['command_id']);
        $command->markCompleted();

        Log::info('ESP32 reported command done', [
            'command_id' => $command->id,
            'type' => $command->type,
            'duration' => $command->duration,
        ]);

        // Update the related schedule or manual feed status
        if ($command->type === 'scheduled' && $command->schedule_id) {
            $schedule = FeedingSchedule::find($command->schedule_id);
            if ($schedule) {
                $schedule->update(['status' => 'done_feeding']);

                // Record to feed history
                $feedHistory = FeedHistory::create([
                    'feed_type' => 'scheduled',
                    'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                    'schedule_id' => $schedule->id,
                    'duration' => $command->duration,
                    'cage_number' => $schedule->cage_number ?? 1,
                    'status' => 'completed',
                    'fed_at' => now(),
                ]);

                // Log to dashboard notifications via JavaScript dispatch
                Log::info('Scheduled feeding completed via ESP32 WiFi', [
                    'schedule_id' => $schedule->id,
                ]);
                
                // Add event for JavaScript notification
                \Session::push('feeder_events', [
                    'type' => 'feeding_success',
                    'status' => 'completed',
                    'message' => 'Scheduled feeding completed - ' . $command->duration . ' seconds',
                    'time' => now()->format('g:i A'),
                    'breed' => optional($schedule->breed)->name ?? 'Unknown',
                ]);
            }
        } elseif ($command->type === 'manual' && $command->manual_feed_id) {
            $manualFeed = ManualFeed::find($command->manual_feed_id);
            if ($manualFeed) {
                $manualFeed->update([
                    'status' => 'done_feeding',
                    'completed_at' => now(),
                ]);

                // Record to feed history
                $feedHistory = FeedHistory::create([
                    'feed_type' => 'manual',
                    'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                    'manual_feed_id' => $manualFeed->id,
                    'duration' => $command->duration,
                    'cage_number' => $manualFeed->cage_number ?? 1,
                    'days' => $manualFeed->days,
                    'status' => 'completed',
                    'fed_at' => now(),
                ]);

                Log::info('Manual feeding completed via ESP32 WiFi', [
                    'manual_feed_id' => $manualFeed->id,
                ]);
                
                // Add event for JavaScript notification
                \Session::push('feeder_events', [
                    'type' => 'feeding_success',
                    'status' => 'completed',
                    'message' => 'Manual feeding completed - ' . $command->duration . ' seconds',
                    'time' => now()->format('g:i A'),
                    'breed' => optional($manualFeed->breed)->name ?? 'Unknown',
                ]);
            }
        }

        // Send SMS notification
        $this->sendSmsNotification($command);

        return response()->json([
            'success' => true,
            'message' => 'Command marked as completed',
        ]);
    }

    /**
     * Send SMS notification after feeding completes
     */
    private function sendSmsNotification(FeederCommand $command)
    {
        $phoneNumber = env('SMS_PHONE_NUMBER');

        if (!$phoneNumber) {
            return;
        }

        try {
            $twilio = new \App\Services\TwilioService();
            $typeLabel = $command->type === 'scheduled' ? 'Scheduled' : 'Manual';
            $message = "✅ SQUIFM: {$typeLabel} feeding completed ({$command->duration} seconds)";

            $twilio->sendSMS($phoneNumber, $message);
        } catch (\Exception $e) {
            Log::error('SMS notification failed: ' . $e->getMessage());
        }
    }
}
