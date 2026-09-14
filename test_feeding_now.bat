@echo off
echo ========================================
echo   TESTING FEEDING SYSTEM NOW
echo ========================================
echo.

echo [1] Checking current time...
php -r "echo date('Y-m-d H:i:s') . PHP_EOL;"
echo.

echo [2] Checking feeding times from database...
php artisan tinker --execute="echo 'Morning: ' . App\Models\FarmSetting::get('feed_time_morning', '08:00') . PHP_EOL; echo 'Afternoon: ' . App\Models\FarmSetting::get('feed_time_afternoon', '12:00') . PHP_EOL; echo 'Evening: ' . App\Models\FarmSetting::get('feed_time_evening', '17:00') . PHP_EOL;"
echo.

echo [3] Checking last auto feed...
if exist storage\logs\last_auto_feed.txt (
    type storage\logs\last_auto_feed.txt
) else (
    echo No last feed recorded
)
echo.

echo [4] Running feeder check command NOW...
php artisan feeder:check-automatic
echo.

echo [5] Checking servo command file...
if exist storage\logs\servo_command.json (
    echo servo_command.json EXISTS:
    type storage\logs\servo_command.json
) else (
    echo servo_command.json NOT FOUND
)
echo.

echo ========================================
echo   TEST COMPLETE
echo ========================================
pause
