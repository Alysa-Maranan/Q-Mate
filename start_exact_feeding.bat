@echo off
REM FAST FEEDING SCHEDULER
REM Checks every 3 seconds for MINIMAL DELAY

title SQUIFM - Fast Auto Feeder
color 0A

echo ========================================
echo   SQUIFM FAST AUTO FEEDER
echo ========================================
echo.
echo Checking every 3 seconds (minimal delay)
echo Press CTRL+C to stop
echo.
echo ========================================
echo.

:loop
php artisan feeder:check-automatic
timeout /t 3 /nobreak > nul
goto loop
