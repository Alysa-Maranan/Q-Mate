@echo off
echo Starting Feeder Check Service...
echo Press Ctrl+C to stop
echo.

:loop
echo [%date% %time%] Running feeder check...
php artisan feeder:check
echo [%date% %time%] Next check in 60 seconds...
timeout /t 60 /nobreak > nul
goto loop
