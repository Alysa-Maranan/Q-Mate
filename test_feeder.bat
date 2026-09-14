@echo off
echo ========================================
echo   QUICK FEEDER TEST
echo ========================================
echo.
echo Testing servo motor connection...
echo.

cd /d "%~dp0"

REM Test servo via artisan command
php artisan feeder:auto-check

echo.
echo ========================================
echo   TEST COMPLETE
echo ========================================
echo.
echo Check if:
echo 1. Servo motor moved
echo 2. Animation appeared in browser
echo 3. Dashboard notification showed
echo.
echo If nothing happened, check:
echo - Servo bridge is running (scripts\start_servo_bridge.bat)
echo - COM port is correct in .env file
echo - Servo motor is connected
echo.
pause
