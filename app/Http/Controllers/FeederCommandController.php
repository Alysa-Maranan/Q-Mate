<?php 

namespace App\Http\Controllers; 

use App\Models\FeederCommand; 
use App\Models\FeedingSchedule; 
use App\Models\ManualFeed; 
use App\Services\FeedHistoryRecorder;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
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

        if (\App\Models\FarmSetting::get('scheduled_feeding_enabled', '1') !== '1') {
            FeederCommand::where('type', 'scheduled')
                ->where('status', 'pending')
                ->update(['status' => 'failed']);
        }

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
    public function commandDone(Request $request, FeedHistoryRecorder $recorder)
    { 
        $validated = $request->validate([ 
            'command_id' => 'required|integer|exists:feeder_commands,id', 
        ]); 

        [$command, $recorded, $processed] = DB::transaction(function () use ($validated, $recorder) {
            $command = FeederCommand::query()
                ->lockForUpdate()
                ->findOrFail($validated['command_id']);

            if ($command->status === 'completed') {
                return [$command, false, true];
            }

            if ($command->status === 'failed') {
                return [$command, false, false];
            }

            if ($command->type === 'scheduled' && $command->status !== 'in_progress') {
                return [$command, false, false];
            }

            $command->markCompleted();
            $recorded = false;

            if ($command->type === 'scheduled' && $command->schedule_id) {
                $schedule = FeedingSchedule::find($command->schedule_id);
                if ($schedule) {
                    $recorded = $recorder->completeScheduled($schedule, $command->duration);
                }
            } elseif ($command->type === 'manual' && $command->manual_feed_id) {
                $manualFeed = ManualFeed::find($command->manual_feed_id);
                if ($manualFeed) {
                    $recorded = $recorder->completeManual($manualFeed, $command->duration);
                }
            }

            return [$command, $recorded, true];
        });

        if (!$processed) {
            return response()->json([
                'success' => false,
                'message' => $command->type === 'scheduled'
                    ? 'Scheduled command must be picked up by the feeder before completion.'
                    : 'Failed feeder commands cannot be recorded as completed.',
            ], 409);
        }

        Log::info('ESP32 reported command done', [ 
            'command_id' => $command->id, 
            'type' => $command->type, 
            'duration' => $command->duration, 
        ]); 

        if ($recorded && $command->type === 'scheduled' && $command->schedule_id) {
            $schedule = FeedingSchedule::find($command->schedule_id); 
            if ($schedule) { 
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
        } elseif (
            $recorded
            && $command->type === 'manual'
            && $command->manual_feed_id
        ) {
            $manualFeed = ManualFeed::find($command->manual_feed_id); 
            if ($manualFeed) { 
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

        if ($recorded) {
            $this->sendSmsNotification($command);
        }

        return response()->json([ 
            'success' => true, 
            'message' => $recorded ? 'Command marked as completed' : 'Command was already completed',
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