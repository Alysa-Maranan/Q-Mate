<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeedingSchedule;
use App\Models\FeedHistory;
use App\Services\TwilioService;
use Carbon\Carbon;

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
        $startedAt = now();
        $port      = env('SERVO_SERIAL_PORT', 'COM3');
        $cmdFile   = storage_path('logs/servo_command.json');

        // ── Never clobber a command the bridge hasn't consumed yet ──
        // Fresh pending file => bridge busy/offline, skip this run.
        // Stale file (>60s) => bridge was down; replace it.
        if (file_exists($cmdFile)) {
            if (time() - filemtime($cmdFile) > 60) {
                @unlink($cmdFile);
                $this->warn('Replaced stale servo_command.json');
            } else {
                $this->warn('servo_command.json still pending — bridge busy/offline, skipping schedule ID ' . $s->id);
                return;
            }
        }

        // 1. Set feeding_now FIRST
        $s->update([
            'status'             => 'feeding_now',
            'last_feed_at'       => $startedAt,
            'feeding_started_at' => $startedAt,
        ]);

        $this->info('Status set to feeding_now for schedule ID: ' . $s->id);

        // 2. Write servo_command.json directly (non-blocking) so servo_bridge.py
        //    can open the servo and create animation_trigger.json at the exact
        //    moment the servo opens.
        file_put_contents($cmdFile, json_encode([
            'action'   => 'trigger',
            'duration' => $amount,
            'port'     => $port,
            'cage'     => $s->cage_number ?? 1,
            'type'     => 'scheduled',
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
                . $s->id . ' will feed as soon as the bridge comes online.');
        }

        // 3. Mark done + record history after the servo finishes via a background
        //    process — do NOT sleep here, that would block the scheduler loop for
        //    the entire feed duration (5 minutes for scheduled feeds).
        $php     = PHP_BINARY;
        $artisan = base_path('artisan');
        $sid     = $s->id;
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" feeder:schedule-done --id={$sid} --duration={$amount} > NUL 2>&1", 'r'));
        } else {
            exec("\"{$php}\" \"{$artisan}\" feeder:schedule-done --id={$sid} --duration={$amount} > /dev/null 2>&1 &");
        }

        $this->info('Background feeder:schedule-done scheduled for schedule ID: ' . $s->id);
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
