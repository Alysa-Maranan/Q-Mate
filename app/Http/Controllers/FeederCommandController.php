<?php

namespace App\Http\Controllers;

use App\Models\FeederCommand;
use App\Models\FeedingSchedule;
use App\Models\ManualFeed;
use App\Models\FeedHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeederCommandController extends Controller
{
    /**
     * GET /api/feeder/pending-command
     * Returns the oldest pending command to the ESP32.
     */
    public function getPendingCommand()
    {
        FeederCommand::cleanupStale();

        $command = FeederCommand::getNextPending();

        if (!$command) {
            return response()->json([
                'has_command' => false,
            ]);
        }

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
     * POST /api/feeder/command-done
     * Records feeding history after the device reports completion.
     * A command that is already completed will not create another record.
     */
    public function commandDone(Request $request)
    {
        $validated = $request->validate([
            'command_id' => [
                'required',
                'integer',
                'exists:feeder_commands,id',
            ],
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                $command = FeederCommand::where(
                    'id',
                    $validated['command_id']
                )->lockForUpdate()->firstOrFail();

                // Prevent duplicate history records for the same command.
                if ($command->status === 'completed') {
                    return [
                        'duplicate' => true,
                        'type' => $command->type,
                        'command_id' => $command->id,
                    ];
                }

                if (
                    !in_array($command->type, ['manual', 'scheduled'], true)
                ) {
                    throw new \RuntimeException(
                        'Unsupported feeder command type.'
                    );
                }

                $historyData = [
                    'feed_type' => $command->type,
                    'feed_brand' => cache(
                        'feed_brand',
                        'Quail Layer Smash'
                    ),
                    'duration' => $command->duration,
                    'cage_number' => 1,
                    'status' => 'completed',
                    'fed_at' => now(),
                ];

                if (
                    $command->type === 'scheduled'
                    && $command->schedule_id
                ) {
                    $schedule = FeedingSchedule::find(
                        $command->schedule_id
                    );

                    if (!$schedule) {
                        throw new \RuntimeException(
                            'The feeding schedule was not found.'
                        );
                    }

                    $schedule->update([
                        'status' => 'done_feeding',
                    ]);

                    $historyData['schedule_id'] = $schedule->id;
                    $historyData['cage_number'] =
                        $schedule->cage_number ?? 1;
                } elseif (
                    $command->type === 'manual'
                    && $command->manual_feed_id
                ) {
                    $manualFeed = ManualFeed::find(
                        $command->manual_feed_id
                    );

                    if (!$manualFeed) {
                        throw new \RuntimeException(
                            'The manual feeding record was not found.'
                        );
                    }

                    $manualFeed->update([
                        'status' => 'done_feeding',
                        'completed_at' => now(),
                    ]);

                    $historyData['manual_feed_id'] = $manualFeed->id;
                    $historyData['cage_number'] =
                        $manualFeed->cage_number ?? 1;
                    $historyData['days'] = $manualFeed->days;
                } else {
                    throw new \RuntimeException(
                        'The feeder command has no associated feed record.'
                    );
                }

                // Save the history and command completion together.
                FeedHistory::create($historyData);

                $command->markCompleted();

                Log::info('Feeding history saved', [
                    'command_id' => $command->id,
                    'type' => $command->type,
                ]);

                return [
                    'duplicate' => false,
                    'type' => $command->type,
                    'command_id' => $command->id,
                    'duration' => $command->duration,
                ];
            });

            if ($result['duplicate']) {
                return response()->json([
                    'success' => true,
                    'duplicate' => true,
                    'message' => 'This feeding command was already recorded.',
                ]);
            }

            $this->sendSmsNotification(
                $result['type'],
                $result['duration']
            );

            return response()->json([
                'success' => true,
                'duplicate' => false,
                'message' => 'Feeding completed and history saved.',
                'command_id' => $result['command_id'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to save feeding history', [
                'command_id' => $validated['command_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save feeding history.',
            ], 500);
        }
    }

    /**
     * Send an SMS notification after a newly completed command.
     */
    private function sendSmsNotification(string $type, int $duration): void
    {
        $phoneNumber = env('SMS_PHONE_NUMBER');

        if (!$phoneNumber) {
            return;
        }

        try {
            $twilio = new \App\Services\TwilioService();

            $typeLabel = $type === 'scheduled'
                ? 'Scheduled'
                : 'Manual';

            $message = "Q-MATE: {$typeLabel} feeding completed "
                . "({$duration} seconds).";

            $twilio->sendSMS($phoneNumber, $message);
        } catch (\Throwable $e) {
            Log::error('SMS notification failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}