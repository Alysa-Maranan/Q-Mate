@echo off
echo ========================================
echo QUICK SCHEDULE TEST - CURRENT MINUTE
echo ========================================
echo.

cd /d "C:\xampp\htdocs\CAPSTONE SQUIFM\my-app"

php -r "require 'vendor/autoload.php'; $app = require 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); $now = now(); $currentMinute = $now->format('H:i:00'); echo 'Current time: ' . $now->format('h:i:s A') . PHP_EOL; echo 'Creating schedule for: ' . $now->format('h:i A') . PHP_EOL . PHP_EOL; $schedule = App\Models\FeedingSchedule::create(['time' => $currentMinute, 'days' => ['sun','mon','tue','wed','thu','fri','sat'], 'amount' => 5, 'enabled' => true]); echo '✅ Schedule created!' . PHP_EOL; echo '   ID: ' . $schedule->id . PHP_EOL; echo '   Time: ' . $schedule->time . PHP_EOL; echo '   Amount: 5 seconds' . PHP_EOL . PHP_EOL; echo '🎯 Now: Open http://127.0.0.1:8000/feeder' . PHP_EOL; echo '⏰ Max delay: 5 SECONDS (checks every 5 seconds)' . PHP_EOL; echo '🎬 Animation will appear when time matches!' . PHP_EOL; echo '🔧 Servo will run at SAME TIME as animation!' . PHP_EOL;"

echo.
echo ========================================
echo System Status:
echo ========================================
echo - Check interval: Every 5 seconds (balanced)
echo - Max trigger delay: 5 seconds
echo - Animation: IMMEDIATE (non-blocking)
echo - Servo: PARALLEL with animation
echo - List order: LATEST FIRST
echo - Performance: OPTIMIZED
echo ========================================
echo.
pause
