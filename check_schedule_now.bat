@echo off
echo ========================================
echo   CHECKING SCHEDULES NOW
echo ========================================
echo.

cd /d "%~dp0"

echo Checking for scheduled feeds...
php artisan feeder:auto-check

echo.
echo Check complete!
echo.
pause
