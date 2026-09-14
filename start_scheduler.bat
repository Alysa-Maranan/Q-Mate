@echo off
title SQUIFM Feeder Scheduler
cd /d "C:\xampp\htdocs\CAPSTONE_SQUIFM"
echo ========================================
echo   SQUIFM FEEDER SCHEDULER STARTED!
echo ========================================
echo.
echo Scheduler is now running...
echo This window must stay OPEN for feeding schedules to work!
echo Press Ctrl+C to stop.
echo ========================================
echo.
php artisan schedule:work
