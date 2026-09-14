@echo off
echo ================================================
echo  SQUIFM Feeder - Auto Scheduler Setup
echo ================================================
echo.

:: Check admin rights
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Please run this as Administrator!
    echo Right-click this file and select "Run as administrator"
    pause
    exit /b 1
)

:: Delete old task if exists
schtasks /delete /tn "SQUIFM_FeederScheduler" /f >nul 2>&1

:: Create new task - runs every minute
schtasks /create /tn "SQUIFM_FeederScheduler" /tr "\"c:\xampp\htdocs\CAPSTONE SQUIFM\run_scheduler.bat\"" /sc minute /mo 1 /ru SYSTEM /f

if %errorLevel% equ 0 (
    echo.
    echo SUCCESS! Feeder scheduler is now running automatically every minute.
    echo Even if the browser is closed, feeding schedules will still trigger.
    echo.
    echo Task Name: SQUIFM_FeederScheduler
    echo Log file:  c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\scheduler.log
) else (
    echo.
    echo ERROR: Failed to create task. Make sure you ran as Administrator.
)

echo.
pause
