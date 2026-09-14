@echo off
echo ========================================
echo   AUTOMATIC FEEDER SETUP
echo ========================================
echo.
echo This will set up automatic feeding at scheduled times.
echo.
echo STEP 1: Starting Servo Bridge...
cd /d "%~dp0scripts"
start /B pythonw servo_bridge.py COM11
echo Servo bridge started on COM11
echo.

echo STEP 2: Setting up Windows Task Scheduler...
cd /d "%~dp0"

REM Create a task that runs every minute to check schedules
schtasks /create /tn "QuailFeederAutoCheck" /tr "php \"%~dp0artisan\" feeder:auto-check" /sc minute /mo 1 /f

echo.
echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo The feeder will now automatically check for schedules every minute.
echo Scheduled feedings will trigger at the exact time you set.
echo.
echo To stop automatic feeding, run: stop_auto_feeder.bat
echo.
pause
