<?php

namespace App\Http\Controllers;

use App\Models\FeedingSchedule;
use App\Models\ManualFeed;
use App\Models\FeedHistory;
use App\Models\FeederCommand;
use App\Services\TwilioService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedingScheduleController extends Controller
{
    /**
     * Scheduled daily feeds (8:00 AM / 12:00 PM / 5:00 PM) keep the servo
     * OPEN for exactly 5 minutes (300 seconds).
     */
    const SCHEDULED_DAILY_FEED_DURATION = 300;

    /**
     * Open the servo by writing a command file for servo_bridge.py.
     * The bridge creates animation_trigger.json at the exact moment the
     * servo opens, synchronizing the browser animation with the hardware.
     */
    public static function openServo($cage = 1, $duration = 5, $feedType = 'manual')
    {
        $cmdFile = storage_path('logs/servo_command.json');
        $port    = env('SERVO_SERIAL_PORT', 'COM3');

        // ── Never clobber a command the bridge hasn't consumed yet ──
        if (file_exists($cmdFile) && (time() - filemtime($cmdFile) <= 60)) {
            Log::warning('openServo: servo_command.json still pending — not overwriting', ['type' => $feedType]);
            return false;
        }
        if (file_exists($cmdFile)) {
            @unlink($cmdFile);
        }

        file_put_contents($cmdFile, json_encode([
            'action'   => 'trigger',
            'duration' => (int) $duration,
            'port'     => $port,
            'cage'     => (int) $cage,
            'type'     => $feedType,
        ]));

        Log::info('openServo() called', ['cage' => $cage, 'duration' => $duration, 'type' => $feedType, 'port' => $port]);
        return true;
    }

    /**
     * Close the servo immediately by writing a close command for servo_bridge.py.
     * The bridge deletes animation_trigger.json at the exact moment the
     * servo closes, synchronizing the browser animation with the hardware.
     */
    public static function closeServo()
    {
        $cmdFile = storage_path('logs/servo_command.json');
        $port    = env('SERVO_SERIAL_PORT', 'COM3');

        file_put_contents($cmdFile, json_encode([
            'action' => 'close',
            'port'   => $port,
        ]));

        Log::info('closeServo() called', ['port' => $port]);
        return true;
    }

    // Get feeding times from database (dynamic)
    public static function getFeedingTimes()
    {
        return [
            \App\Models\FarmSetting::get('feed_time_morning', '08:00'),
            \App\Models\FarmSetting::get('feed_time_afternoon', '12:00'),
            \App\Models\FarmSetting::get('feed_time_evening', '17:00'),
        ];
    }

    public function index()
    {
        // Auto-create daily feeding schedules if they don't exist
        $this->ensureDailySchedulesExist();

        $feedHistory = FeedHistory::select('id', 'feed_type', 'feed_brand', 'duration', 'status', 'fed_at', 'cage_number')
            ->latest('fed_at')
            ->limit(10)
            ->get();

        $feedBrand = cache('feed_brand', 'Quail Layer Smash');

        // Stats
        $fedToday = FeedHistory::where('status', 'completed')
            ->whereDate('fed_at', today())
            ->count();

        $fedThisWeek = FeedHistory::where('status', 'completed')
            ->whereBetween('fed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Compute next feeding time
        $now = now();
        $nextFeeding = null;
        $feedTimes = self::getFeedingTimes();
        foreach ($feedTimes as $t) {
            $candidate = \Carbon\Carbon::createFromFormat('H:i', $t)->setDateFrom($now);
            if ($candidate->gt($now)) {
                $nextFeeding = $candidate;
                break;
            }
        }
        if (!$nextFeeding) {
            $nextFeeding = \Carbon\Carbon::createFromFormat('H:i', $feedTimes[0])->addDay()->setDateFrom($now->copy()->addDay());
        }

        // Get current quail breed
        $currentQuailBreed = \App\Helpers\QuailBreedHelper::getCurrentBreed();
        $currentBreeds = \App\Helpers\QuailBreedHelper::getCurrentBreeds();

        // Prepare feeding times with labels and SVG icons
        $feedTimesWithLabels = [];
        foreach ($feedTimes as $time) {
            $hour = (int)substr($time, 0, 2);
            $formattedTime = \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');

            if ($hour < 12) {
                // Morning - Sunrise SVG icon
                $icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
            } elseif ($hour < 17) {
                // Afternoon - Sun SVG icon
                $icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path></svg>';
            } else {
                // Evening - Sunset/Moon SVG icon
                $icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
            }

            $label = '<span style="display:inline-flex;align-items:center;gap:0.4rem;">' . $icon . ' ' . $formattedTime . '</span>';
            $feedTimesWithLabels[$time] = $label;
        }

        return view('feeder.index', compact('feedHistory', 'feedBrand', 'nextFeeding', 'fedToday', 'fedThisWeek', 'currentQuailBreed', 'currentBreeds', 'feedTimes', 'feedTimesWithLabels'));
    }

    /**
     * Ensure daily feeding schedules exist in database
     * Creates schedules for morning, afternoon, and evening if they don't exist
     */
    public static function ensureDailySchedulesExist()
    {
        $feedTimes = self::getFeedingTimes();
        
        foreach ($feedTimes as $time) {
            $timeWithSeconds = $time . ':00';
            
            // Check if schedule exists for this time
            $existingSchedule = FeedingSchedule::where('time', $timeWithSeconds)
                ->where('cage_number', 1)
                ->first();
            $exists = (bool) $existingSchedule;
            
            if (!$exists) {
                // Create schedule with 5-minute servo duration
                FeedingSchedule::create([
                    'time' => $timeWithSeconds,
                    'days' => [], // Empty array means every day
                    'amount' => self::SCHEDULED_DAILY_FEED_DURATION, // 300 seconds = 5 minutes
                    'cage_number' => 1,
                    'enabled' => \DB::raw('TRUE'),
                    'status' => 'idle',
                ]);
                
                Log::info('Auto-created daily feeding schedule', ['time' => $time]);
            } elseif ((int) $existingSchedule->amount !== self::SCHEDULED_DAILY_FEED_DURATION) {
                // Keep the built-in daily schedules at the five-minute duration
                $existingSchedule->update(['amount' => self::SCHEDULED_DAILY_FEED_DURATION]);
                Log::info('Updated daily feeding schedule to 5-minute duration', ['time' => $time]);
            }
        }
    }

    public function toggleFeeder()
    {
        $current = cache('feed_brand', 'Quail Layer Smash');
        $next = $current === 'Quail Layer Smash' ? 'Jedstar' : 'Quail Layer Smash';
        cache(['feed_brand' => $next], now()->addYears(10));
        return redirect()->route('feeder')->with('success', 'Now using ' . $next . '.');
    }

    /**
     * Get current feeding status - called by frontend polling
     */
    public function getStatus()
    {
        // Clean up stale feeding_now (older than 2 min)
        FeedingSchedule::where('status', 'feeding_now')
            ->where('updated_at', '<', now()->subMinutes(2))
            ->update(['status' => 'idle']);

        FeederCommand::whereIn('status', ['pending', 'in_progress'])
            ->where('created_at', '<', now()->subMinutes(2))
            ->update(['status' => 'failed']);

        $feedingNow = FeedingSchedule::where('status', 'feeding_now')->first();
        $recentlyDone = FeedingSchedule::where('status', 'done_feeding')->first();

        if ($feedingNow) {
            return response()->json([
                'status' => 'feeding_now',
                'schedule_id' => $feedingNow->id,
                'time' => $feedingNow->time,
                'amount' => $feedingNow->amount,
                'message' => 'Feeding in progress...'
            ]);
        }

        if ($recentlyDone) {
            return response()->json([
                'status' => 'done_feeding',
                'schedule_id' => $recentlyDone->id,
                'time' => $recentlyDone->time,
                'amount' => $recentlyDone->amount,
                'message' => 'Feeding completed!'
            ]);
        }

        return response()->json([
            'status' => 'idle',
            'message' => 'No active feeding'
        ]);
    }

    public function checkSchedule()
{
    // Release session lock immediately so polling requests aren't blocked
    if (session()->isStarted()) {
        session()->save();
    }

    $now = now();
    $todayKey = strtolower(substr($now->format('D'), 0, 3));
    $currentHM = $now->format('H:i');

    // ── Self-heal schedule statuses ─────────────────────────────────

    // 1. Finished feeds return to idle so RECURRING schedules
    //    can fire again at their next occurrence.
    FeedingSchedule::where('status', 'done_feeding')
        ->where(function ($q) {
            $q->whereNull('last_feed_at')
              ->orWhere('last_feed_at', '<', now()->subMinutes(2));
        })
        ->update(['status' => 'idle']);

    // 2. Release stale scheduled feeds so they cannot block
    //    other schedules forever.
    FeedingSchedule::where('status', 'feeding_now')
        ->where('feeding_started_at', '<', now()->subMinutes(30))
        ->update(['status' => 'idle']);

    // 3. Keep existing Manual Feed self-healing untouched.
    ManualFeed::where('status', 'feeding_now')
        ->where('created_at', '<', now()->subMinutes(30))
        ->update([
            'status' => 'done_feeding',
            'completed_at' => now()
        ]);

    // ── Find schedules that are ready to run ─────────────────────────

    $schedules = FeedingSchedule::where('enabled', true)
        ->whereIn('status', ['idle'])
        ->whereNotExists(function ($q) {
            $q->from('feeding_schedules')
                ->where('status', 'feeding_now');
        })
        ->get();

    $triggered = [];

    foreach ($schedules as $schedule) {

        // Check scheduled time
        $schedHM = substr($schedule->time, 0, 5);

        if ($schedHM !== $currentHM) {
            continue;
        }

        // Check scheduled day
        $days = $schedule->days ?? [];

        if (!empty($days) && is_array($days)) {
            if (!in_array($todayKey, $days)) {
                continue;
            }
        }

        // Prevent duplicate triggering within 120 seconds
        if (
            $schedule->last_feed_at &&
            $now->diffInSeconds($schedule->last_feed_at) < 120
        ) {
            continue;
        }

        // ── START SCHEDULED FEED ─────────────────────────────────────

        $startedAt = now();
        $amount = $schedule->amount ?? 5;
        $cage = $schedule->cage_number ?? 1;

        // Mark schedule as feeding now
        $schedule->update([
            'status'             => 'feeding_now',
            'last_feed_at'       => $startedAt,
            'feeding_started_at' => $startedAt,
        ]);

        // Tell frontend that a scheduled feeding was triggered.
        $triggered[] = [
            'id'     => $schedule->id,
            'time'   => $schedule->time,
            'amount' => $amount,
            'cage'   => $cage,
        ];

        // ── WIFI MODE ────────────────────────────────────────────────
        // Queue the command exactly like the working Feed Now system,
        // but using the existing scheduled command method.
        $command = FeederCommand::queueScheduled(
            $schedule->id,
            $amount
        );

        Log::info('Scheduled feeder command queued via WiFi', [
            'schedule_id' => $schedule->id,
            'command_id'  => $command->id,
            'duration'    => $amount,
            'cage'        => $cage,
        ]);
    }

    return response()->json([
        'status'    => 'ok',
        'triggered' => $triggered,
        'count'     => count($triggered),
    ]);
}

    /**
     * Backward-compatible alias for checkSchedule().
     */
    public function autoCheckSchedules()
    {
        return $this->checkSchedule();
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_datetime' => 'required|date_format:Y-m-d\TH:i',
            'amount' => 'required|integer|min:1|max:60',
            'cage_number' => 'required|integer|min:1|max:10',
        ]);

        $dt = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->schedule_datetime);

        if ($dt->copy()->subMinute()->isPast() && $dt->isPast()) {
            return redirect()->route('feeder')->withFragment('schedule')->with('error', 'Cannot set a schedule in the past.');
        }

        $time = $dt->format('H:i');
        $day = strtolower($dt->format('D'));

        $existingSchedule = FeedingSchedule::where('time', $time . ':00')
            ->where('cage_number', $request->cage_number)
            ->first();
        if ($existingSchedule) {
            return redirect()->route('feeder')->withFragment('schedule')->with('error', 'A schedule at this time already exists for Cage ' . $request->cage_number . '.');
        }

        $schedule = FeedingSchedule::create([
            'time'        => $time,
            'days'        => [$day],
            'amount'      => $request->amount,
            'cage_number' => $request->cage_number,
            'enabled'     => true,
        ]);

        // Only trigger immediately if scheduled time is now or within the past 30 seconds
        $now = now();
        $fmt = strlen($schedule->time) > 5 ? 'H:i:s' : 'H:i';
        $scheduleTime = \Carbon\Carbon::createFromFormat($fmt, $schedule->time)->setDateFrom($now);
        $diffSeconds = $scheduleTime->diffInSeconds($now, false); // positive = scheduleTime is in the past

        if ($diffSeconds >= 0 && $diffSeconds <= 30) {
            $startedAt = now();
            $schedule->update([
                'status'             => 'feeding_now',
                'last_feed_at'       => $startedAt,
                'feeding_started_at' => $startedAt,
            ]);

            $php     = PHP_BINARY;
            $artisan = base_path('artisan');
            if (PHP_OS_FAMILY === 'Windows') {
                shell_exec("start /B \"\" \"{$php}\" \"{$artisan}\" feeder:check --id={$schedule->id} > NUL 2>&1");
            } else {
                shell_exec("\"{$php}\" \"{$artisan}\" feeder:check --id={$schedule->id} > /dev/null 2>&1 &");
            }

            return redirect()->route('feeder')->withFragment('schedule')->with('success', 'Feeding started now for Cage ' . $request->cage_number . '!');
        }

        return redirect()->route('feeder')->withFragment('schedule')->with('success', 'Schedule added successfully for Cage ' . $request->cage_number . '.');
    }

    public function destroy(FeedingSchedule $feedingSchedule)
    {
        $feedingSchedule->delete();
        return redirect()->route('feeder')->withFragment('schedule')->with('success', 'Schedule deleted.');
    }

    public function deleteHistory(FeedHistory $feedHistory)
    {
        $feedHistory->delete();
        return redirect()->route('feeder')->withFragment('history')->with('success', 'History record deleted.');
    }


    public function triggerNow(FeedingSchedule $feedingSchedule)
    {
        $startedAt = now();
        $amount = $feedingSchedule->amount ?? 5;
        
        $feedingSchedule->update([
            'status' => 'feeding_now',
            'last_feed_at' => $startedAt,
            'feeding_started_at' => $startedAt,
        ]);

        // Check FEEDER_MODE: 'wifi' uses ESP32 polling, 'serial' uses Python script
        if (env('FEEDER_MODE', 'serial') === 'wifi') {
            FeederCommand::queueScheduled($feedingSchedule->id, $amount);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'status' => 'queued',
                    'id' => $feedingSchedule->id,
                    'message' => 'Feed command queued. ESP32 will pick it up shortly.',
                ]);
            }
            return redirect()->back()->with('success', 'Feed command queued for ESP32.');
        }

        // Serial mode: Write servo command and trigger background process
        $cmdFile = storage_path('logs/servo_command.json');
        $port = $this->detectSerialPort();
        file_put_contents($cmdFile, json_encode([
            'action' => 'trigger',
            'duration' => $amount,
            'port' => $port,
            'cage' => $feedingSchedule->cage_number ?? 1,
            'type' => 'scheduled',
        ]));

        Log::info('Manual trigger: servo_command.json written for schedule ID: ' . $feedingSchedule->id);

        // Background process will mark done + record history
        $php = PHP_BINARY;
        $artisan = base_path('artisan');
        $sid = $feedingSchedule->id;
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" feeder:schedule-done --id={$sid} --duration={$amount} > NUL 2>&1", 'r'));
        } else {
            exec("\"{$php}\" \"{$artisan}\" feeder:schedule-done --id={$sid} --duration={$amount} > /dev/null 2>&1 &");
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['status' => 'ok', 'id' => $feedingSchedule->id]);
        }

        return redirect()->back()->with('success', 'Feeding started for Cage ' . $feedingSchedule->cage_number . '!');
    }

    public function manualFeed(Request $request)
    {


        $data = $request->validate([
            'duration' => 'required|integer|min:1|max:30',
            'cage_number' => 'required|integer|min:1|max:10',
            'days' => 'nullable|array',
            'force_serial' => 'nullable|boolean',
            'time' => 'nullable|date_format:H:i',
            'date' => 'nullable|date',
        ]);

        // If time and date are set, prevent manual feed for past time today
        if (!empty($data['time']) && (!isset($data['date']) || $data['date'] === date('Y-m-d'))) {
            $selectedTime = \Carbon\Carbon::createFromFormat('H:i', $data['time']);
            $now = now();
            if ($selectedTime->lessThan($now->copy()->setTime($now->hour, $now->minute))) {
                return redirect()->back()->with('error', 'Cannot set manual feed for a past time today.');
            }
        }

        $data['days'] = $data['days'] ?? [];
        $data['status'] = 'feeding_now';
        
        // Check if force_serial is requested (via query param or form field)
        $forceSerial = $request->boolean('force_serial') || $request->query('force_serial') === '1';

        // A failed serial/Wi-Fi attempt must not permanently block the next feed.
        ManualFeed::where('status', 'feeding_now')
            ->where('created_at', '<', now()->subMinutes(2))
            ->update(['status' => 'failed']);
        $animationFile = storage_path('logs/animation_trigger.json');
        if (file_exists($animationFile) && filemtime($animationFile) < time() - 120) {
            @unlink($animationFile);
        }

        $activeSchedule = FeedingSchedule::where('status', 'feeding_now')->exists();
        $activeManual = ManualFeed::where('status', 'feeding_now')->exists();
        if ($activeSchedule || $activeManual || file_exists($animationFile)) {
            Log::warning('Manual feed rejected because another feeding cycle is active.');
            return response()->json([
                'status' => 'skipped',
                'message' => 'Another feeding cycle is already in progress.',
            ], 409);
        }

        Log::info('Manual Feed button request received', [
            'duration' => $data['duration'],
            'cage' => $data['cage_number'],
        ]);
        $manualFeed = ManualFeed::create($data);

        $feederMode = env('FEEDER_MODE', 'wifi');

        // If force_serial is true, skip WiFi and go directly to serial
        if ($forceSerial || $feederMode === 'serial') {
            // Trigger servo via startManualFeed() — servo_bridge.py creates
            // animation_trigger.json at the exact moment the servo opens, and the
            // browser polling starts the modal animation simultaneously.
            $started = $this->startManualFeed($manualFeed);

            // Always return JSON so the browser can show the animation
            return response()->json([
                'status'      => $started ? 'feeding_now' : 'skipped',
                'mode'        => 'serial',
                'duration'    => $manualFeed->duration,
                'cage_number' => $manualFeed->cage_number,
                'message'     => $started
                    ? 'Manual feeding started for Cage ' . $manualFeed->cage_number . '.'
                    : 'Manual feed skipped — a scheduled feeding command is already in progress for the servo.',
            ]);
        }

        // WiFi mode: Queue command for ESP32 to pick up
        $command = FeederCommand::queueManual($manualFeed->id, $manualFeed->duration);

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'queued',
                'command_id' => $command->id,
                'mode' => 'wifi',
                'duration' => $manualFeed->duration,
                'cage_number' => $manualFeed->cage_number,
                'message' => 'Manual feed command queued for Cage ' . $manualFeed->cage_number . '. ESP32 will pick it up shortly.',
            ]);
        }
        return redirect()->back()->with('success', 'Manual feed command queued for Cage ' . $manualFeed->cage_number . ' for ESP32.');
    }


    /**
     * Try to trigger feeding via serial (USB) connection.
     * Returns true if successful, false if serial is unavailable.
     */
    private function trySerialFeeding(ManualFeed $manualFeed): bool
    {
        $port = $this->detectSerialPort();
        $python = env('PYTHON_PATH', 'python');
        $script = base_path('scripts/servo_control.py');

        // Check if the Python script exists
        if (!file_exists($script)) {
            Log::warning('Serial fallback: servo_control.py not found', ['script' => $script]);
            return false;
        }

        $command = "\"{$python}\" \"{$script}\" trigger {$manualFeed->duration} {$port}";
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0) {
            $manualFeed->update([
                'status' => 'done_feeding',
                'completed_at' => now(),
            ]);

            FeedHistory::create([
                'feed_type' => 'manual',
                'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                'manual_feed_id' => $manualFeed->id,
                'duration' => $manualFeed->duration,
                'cage_number' => $manualFeed->cage_number,
                'days' => $manualFeed->days,
                'status' => 'completed',
                'fed_at' => now(),
            ]);

            $this->sendSmsNotification("Manual feeding completed: {$manualFeed->duration} seconds");
            return true;
        }

        Log::warning('Serial fallback failed', [
            'output' => $output,
            'returnCode' => $returnCode,
            'command' => $command,
        ]);
        return false;
    }

    private function startManualFeed(ManualFeed $manualFeed): bool
    {
        $port   = $this->detectSerialPort();
        $duration = $manualFeed->duration;
        $cage = $manualFeed->cage_number;
        $cmdFile = storage_path('logs/servo_command.json');

        // ── Never clobber a command the bridge hasn't consumed yet ──
        // If a scheduled feed command is pending (bridge busy/offline),
        // do NOT overwrite it — the scheduled feed takes priority. A stale
        // file (>60s, bridge down) is replaced so the manual feed is not lost.
        if (file_exists($cmdFile)) {
            if (time() - filemtime($cmdFile) > 60) {
                @unlink($cmdFile);
                Log::warning('startManualFeed: replaced stale servo_command.json');
            } else {
                Log::warning('startManualFeed: servo_command.json still pending — manual feed skipped to protect scheduled feed');
                return false;
            }
        }

        // Write servo_command.json - servo_bridge.py creates animation_trigger.json
        // for ALL feed types (manual + scheduled) at the exact moment the servo opens,
        // synchronizing the browser modal animation with the physical servo.
        $command = json_encode([
            'action'   => 'trigger',
            'duration' => $duration,
            'port'     => $port,
            'cage'     => $cage,
            'type'     => 'manual',  // servo_bridge.py creates animation for ALL types
        ]);
        $temporaryCommandFile = $cmdFile . '.tmp.' . getmypid();
        file_put_contents($temporaryCommandFile, $command, LOCK_EX);
        rename($temporaryCommandFile, $cmdFile);

        $manualFeed->update(['started_at' => now()]);
        Log::info('Manual feeding function triggered; servo command sent', [
            'manual_feed_id' => $manualFeed->id,
            'port' => $port,
            'duration' => $duration,
        ]);

        // Schedule status update after feeding completes
        $php     = PHP_BINARY;
        $artisan = base_path('artisan');
        $id      = $manualFeed->id;
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen("start /B \"\" \"{$php}\" \"{$artisan}\" feeder:manual-done --id={$id} --duration={$duration} > NUL 2>&1", 'r'));
        } else {
            exec("\"{$php}\" \"{$artisan}\" feeder:manual-done --id={$id} --duration={$duration} > /dev/null 2>&1 &");
        }
        return true;
    }

    /**
     * Backward-compatible alias for startManualFeed().
     */
    private function triggerManualFeeding(ManualFeed $manualFeed)
    {
        return $this->startManualFeed($manualFeed);
    }

    /**
     * Detect the correct serial port to use
     */
    private function detectSerialPort()
    {
        // Try environment variable first
        $port = env('SERVO_SERIAL_PORT');
        if ($port) {
            return $port;
        }
        
        // Check config
        $port = config('app.servo_serial_port');
        if ($port) {
            return $port;
        }
        
        // Default to COM3 for Windows
        return 'COM3';
    }

    /**
     * Detect the correct Python path
     */
    private function detectPythonPath()
    {
        // Try environment variable first
        $python = env('PYTHON_PATH');
        if ($python && $this->testPythonCommand($python)) {
            return $python;
        }
        
        // Try common Windows Python commands
        $candidates = ['python', 'py', 'python3', 'C:\Python39\python.exe', 'C:\Python310\python.exe', 'C:\Python311\python.exe'];
        
        foreach ($candidates as $candidate) {
            if ($this->testPythonCommand($candidate)) {
                return $candidate;
            }
        }
        
        // Default to 'python'
        return 'python';
    }

    /**
     * Test if a Python command works
     */
    private function testPythonCommand($command)
    {
        $testCmd = $command . ' --version 2>&1';
        exec($testCmd, $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Trigger a schedule in background (non-blocking)
     * This allows the API response to return immediately while servo runs
     */
    private function triggerScheduleInBackground($scheduleId)
    {
        // Get the path to PHP and artisan
        $php = PHP_BINARY; // Path to current PHP executable
        $artisan = base_path('artisan');
        
        // Build command to run in background
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Use START /B for background process
            $command = "start /B \"\" \"$php\" \"$artisan\" feeder:check --id=$scheduleId > NUL 2>&1";
        } else {
            // Linux/Mac: Use & for background process
            $command = "$php \"$artisan\" feeder:check --id=$scheduleId > /dev/null 2>&1 &";
        }
        
        // Execute in background (non-blocking)
        exec($command);
        
        Log::info('Triggered schedule in background', ['schedule_id' => $scheduleId, 'command' => $command]);
    }

    /**
     * Directly trigger a schedule - runs the Python script directly
     * This is more reliable than background execution
     */
    private function triggerScheduleDirect($scheduleId, $amount = 5)
    {
        $schedule = FeedingSchedule::find($scheduleId);
        if (!$schedule) {
            Log::error('Schedule not found for direct trigger', ['schedule_id' => $scheduleId]);
            return false;
        }

        $port = $this->detectSerialPort();
        $python = env('PYTHON_PATH', 'python');
        $script = base_path('scripts/servo_control.py');

        // Check if the Python script exists
        if (!file_exists($script)) {
            Log::error('servo_control.py not found', ['script' => $script]);
            $schedule->update(['status' => 'idle']);
            return false;
        }

        $command = "\"$python\" \"$script\" trigger $amount $port";
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode === 0) {
            // Success - update status to done_feeding
            $schedule->update(['status' => 'done_feeding']);
            
            // Record to feed history
            FeedHistory::create([
                'feed_type' => 'scheduled',
                'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                'schedule_id' => $schedule->id,
                'duration' => $amount,
                'cage_number' => $schedule->cage_number,
                'status' => 'completed',
                'fed_at' => \Carbon\Carbon::createFromFormat(strlen($schedule->time) > 5 ? 'H:i:s' : 'H:i', $schedule->time)->setDateFrom(now()),
            ]);

            // Send SMS notification
            $this->sendSmsNotification("Scheduled feeding completed at {$schedule->time} for {$amount} seconds");
            
            Log::info('Direct schedule trigger succeeded', [
                'schedule_id' => $scheduleId,
                'amount' => $amount,
                'output' => $output
            ]);
            return true;
        } else {
            // Failed - update status to idle
            $schedule->update(['status' => 'idle']);
            
            // Record failed feeding to history
            FeedHistory::create([
                'feed_type' => 'scheduled',
                'feed_brand' => cache('feed_brand', 'Quail Layer Smash'),
                'schedule_id' => $schedule->id,
                'duration' => $amount,
                'cage_number' => $schedule->cage_number,
                'status' => 'failed',
                'fed_at' => now(),
            ]);
            
            Log::error('Direct schedule trigger failed', [
                'schedule_id' => $scheduleId,
                'output' => $output,
                'returnCode' => $returnCode,
                'command' => $command
            ]);
            return false;
        }
    }

    private function sendSmsNotification($message)
    {
        $phoneNumber = env('SMS_PHONE_NUMBER');
        
        if (!$phoneNumber) {
            Log::warning('SMS_PHONE_NUMBER not set in .env, skipping SMS');
            return;
        }

        try {
            $twilio = new TwilioService();
            $result = $twilio->sendSMS($phoneNumber, $message);

            if ($result['success']) {
                Log::info('SMS sent successfully: ' . $message);
            } else {
                Log::error('SMS sending failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception: ' . $e->getMessage());
        }
    }
}
