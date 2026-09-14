@echo off
REM Automatic Feeding Scheduler
REM Runs every 3 SECONDS to check if it's time to feed (MINIMAL DELAY)

echo ========================================
echo   AUTOMATIC FEEDING SCHEDULER
echo   Checking every 3 seconds (minimal delay)
echo ========================================
echo.

:loop
php artisan feeder:check-automatic
timeout /t 3 /nobreak > nul
goto loop
