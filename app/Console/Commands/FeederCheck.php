<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedingSchedule;
use App\Models\FeederCommand;
use App\Services\FeedHistoryRecorder;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeederCheck extends Command
{
    protected $signature = 'feeder:check {--id=}';
    protected $description = 'Check feeder schedules and trigger servo when schedule matches';

    public function handle()
    {
        $id = $this->option('id');

        if ($id) {
            $schedule = FeedingSchedule::find($id);
            if (!$schedule) {
                $this->error('Schedule not found: ' . $id);
                return 1;
            }
            $this->triggerSchedule($schedule);
            return 0;
        }

        $this->finalizeScheduledCommands();

        if (\App\Models\FarmSetting::get('scheduled_feeding_enabled', '1') !== '1') {
            return 0;
        }

        $enabledAt = \App\Models\FarmSetting::get('scheduled_feeding_enabled_at');
        if ($enabledAt !== '' && Carbon::parse($enabledAt)->format('Y-m-d H:i') === now()->format('Y-m-d H:i')) {
            return 0;
        }

        \App\Http\Controllers\FeedingScheduleController::ensureDailySchedulesExist();

        $now = Carbon::now();
        $todayKey = strtolower(substr($now->format('D'), 0, 3));
        $currentHM = $now->format('H:i');

        // ── Self-heal schedule statuses ─────────────────────────────────
        // Finished feeds return to idle so recurring schedules fire again;
        // stale feeding_now (bridge never consumed the command) is released.
        FeedingSchedule::where('status', 'done_feeding')
            ->where(function ($q) {
                $q->whereNull('last_feed_at')
                  ->orWhere('last_feed_at', '<', now()->subMinutes(2));
            })
            ->update(['status' => 'idle']);

        FeedingSchedule::where('status', 'feeding_now')
            ->where('feeding_started_at', '<', now()->subMinutes(30))
            ->update(['status' => 'idle']);

        // Stale manual feeds stuck in feeding_now are closed out.
        \App\Models\ManualFeed::where('status', 'feeding_now')
            ->where('created_at', '<', now()->subMinutes(30))
            ->update(['status' => 'done_feeding', 'completed_at' => now()]);

        $schedules = FeedingSchedule::where('enabled', true)->whereIn('status', ['idle'])->get();

        foreach ($schedules as $s) {
            if (substr($s->time, 0, 5) !== $currentHM) continue;

            $days = $s->days ?? [];
            if (!empty($days) && is_array($days) && !in_array($todayKey, $days)) continue;

            if ($s->last_feed_at && $now->diffInSeconds($s->last_feed_at) < 120) continue;

            $this->triggerSchedule($s);
        }

        return 0;
    }

    protected function triggerSchedule(FeedingSchedule $s)
    {
        $amount    = $s->amount ?? 5;
        $port      = env('SERVO_SERIAL_PORT', 'COM3');
        $cmdFile   = storage_path('logs/servo_command.json');
        $wifiMode  = env('FEEDER_MODE', 'serial') === 'wifi';

        // ── Never clobber a command the bridge hasn't consumed yet ──
        // Fresh pending file => bridge busy/offline, skip this run.
        // Stale file (>60s) => bridge was down; replace it.
        if (!$wifiMode && file_exists($cmdFile)) {
            if (time() - filemtime($cmdFile) > 60) {
                @unlink($cmdFile);
                $this->warn('Replaced stale servo_command.json');
            } else {
                $this->warn('servo_command.json still pending — bridge busy/offline, skipping schedule ID ' . $s->id);
                return;
            }
        }

        [$started, $startedAt, $schedule] = DB::transaction(function () use ($s) {
            $schedule = FeedingSchedule::query()->lockForUpdate()->find($s->id);
            if (!$schedule || $schedule->status !== 'idle'
                || FeedingSchedule::where('status', 'feeding_now')->exists()) {
                return [false, null, null];
            }

            $startedAt = now();
            $schedule->update([
                'status' => 'feeding_now',
                'last_feed_at' => $startedAt,
                'feeding_started_at' => $startedAt,
            ]);

            return [true, $startedAt, $schedule];
        });

        if (!$started) {
            return;
        }

        $eventId = 'schedule_' . $schedule->id . '_' . $startedAt->timestamp;
        $this->info('Status set to feeding_now for schedule ID: ' . $schedule->id);

        if ($wifiMode) {
            FeederCommand::queueScheduled($schedule->id, $amount);
            $this->info('Scheduled feeder command queued for ESP32 WiFi');
            return;
        }

        file_put_contents($cmdFile, json_encode([
            'action'   => 'trigger',
            'duration' => $amount,
            'port'     => $port,
            'cage'     => $schedule->cage_number ?? 1,
            'type'     => 'scheduled',
            'event_id' => $eventId,
        ]));
        $this->info("Feeding function triggered; servo command sent on {$port}");

        $this->info("servo_command.json written for {$amount}s on {$port}");

        // ── Delivery check ─────────────────────────────────────────────
        $consumed = false;
        for ($i = 0; $i < 8; $i++) {
            usleep(500000); // 0.5s
            if (!file_exists($cmdFile)) {
                $consumed = true;
                break;
            }
        }
        if (!$consumed) {
            $this->warn('Servo bridge did NOT consume the command within 4s — schedule ID '
                . $schedule->id . '; running software-only schedule.');
            $pendingCommand = is_file($cmdFile)
                ? json_decode((string) file_get_contents($cmdFile), true)
                : null;
            if (($pendingCommand['event_id'] ?? null) === $eventId) {
                @unlink($cmdFile);
            }
            $simulationOption = '--simulation';
        } else {
            $simulationOption = '';
        }

        $php     = PHP_BINARY;
        $artisan = base_path('artisan');
        $sid     = $schedule->id;
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" feeder:schedule-done --id={$sid} --duration={$amount} --event-id={$eventId} {$simulationOption} > NUL 2>&1", 'r'));
        } else {
            exec("\"{$php}\" \"{$artisan}\" feeder:schedule-done --id={$sid} --duration={$amount} --event-id={$eventId} {$simulationOption} > /dev/null 2>&1 &");
        }

        $this->info('Background feeder:schedule-done scheduled for schedule ID: ' . $schedule->id);
    }

    private function finalizeScheduledCommands(): void
    {
        $recorder = app(FeedHistoryRecorder::class);
        FeedingSchedule::where('status', 'feeding_now')
            ->whereNotNull('feeding_started_at')
            ->get()
            ->each(function (FeedingSchedule $schedule) use ($recorder) {
                $duration = (int) ($schedule->amount ?? 5);
                $finishedAt = $schedule->feeding_started_at->copy()->addSeconds($duration);
                $command = FeederCommand::where('schedule_id', $schedule->id)
                    ->where('type', 'scheduled')
                    ->whereIn('status', ['pending', 'in_progress', 'failed'])
                    ->where('created_at', '>=', $schedule->feeding_started_at)
                    ->latest('id')
                    ->first();

                if (!$command) {
                    if (env('FEEDER_MODE', 'serial') !== 'serial' || $finishedAt->isFuture()) {
                        return;
                    }

                    $eventId = 'schedule_' . $schedule->id . '_' . $schedule->feeding_started_at->timestamp;
                    $resultPath = storage_path('logs/servo_result.json');
                    $result = is_file($resultPath)
                        ? json_decode((string) file_get_contents($resultPath), true)
                        : null;

                    if (($result['event_id'] ?? null) !== $eventId) {
                        $recorder->finalizeScheduledRun($schedule, $duration, 'simulated');
                        return;
                    }

                    if (($result['status'] ?? null) === 'in_progress') {
                        if ($finishedAt->copy()->addSeconds(30)->isFuture()) {
                            return;
                        }
                        $status = 'failed';
                    } else {
                        $status = ($result['success'] ?? false) === true ? 'completed' : 'failed';
                    }

                    $recorder->finalizeScheduledRun($schedule, $duration, $status);
                    return;
                }

                $finishedAt = $schedule->feeding_started_at->copy()->addSeconds((int) $command->duration);
                if (
                    in_array($command->status, ['pending', 'failed'], true)
                    && !$command->picked_up_at
                    && $finishedAt->lte(now())
                ) {
                    $recorder->completeScheduledSimulation($schedule, $command, (int) $command->duration);
                } elseif (
                    in_array($command->status, ['in_progress', 'failed'], true)
                    && $command->picked_up_at
                    && $finishedAt->copy()->addSeconds(30)->lte(now())
                ) {
                    $recorder->failScheduled($schedule, $command, (int) $command->duration);
                }
            });
    }

    protected function sendSmsNotification($message)
    {
        $phoneNumber = '+639916624892';
        try {
            $twilio = new TwilioService();
            $result = $twilio->sendSMS($phoneNumber, $message);
            if (!$result['success']) {
                $this->error('SMS failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            $this->error('SMS exception: ' . $e->getMessage());
        }
    }
}
