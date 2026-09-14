@echo off
echo ========================================
echo   Daily Feeding Auto-Check System
echo ========================================
echo.
echo This will check for scheduled feedings every 60 seconds.
echo Press Ctrl+C to stop.
echo.
echo Starting auto-check...
echo.

:loop
curl -s http://localhost/feeder/schedules/auto-check
echo [%date% %time%] Checked for scheduled feedings
timeout /t 60 /nobreak > nul
goto loop
