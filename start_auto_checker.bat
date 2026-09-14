@echo off
echo ========================================
echo   AUTO SCHEDULE CHECKER STARTED
echo ========================================
echo.
echo This will check schedules every minute.
echo Keep this window open!
echo Press Ctrl+C to stop.
echo.

cd /d "%~dp0"

:loop
echo [%date% %time%] Checking schedules...
php artisan feeder:check
timeout /t 60 /nobreak >nul
goto loop
