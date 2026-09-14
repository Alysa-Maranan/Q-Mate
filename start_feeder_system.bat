@echo off
title Feeder System Starter
cd /d "%~dp0"
color 0A
echo ========================================
echo    FEEDER SYSTEM - COMPLETE STARTER
echo ========================================
echo.

REM Start the unified bridge (visible window - python.exe, gaya ng manual mong run)
echo [STARTING] Unified hardware bridge...
start "SQUIFM Hardware Bridge" /B python scripts\unified_bridge.py COM11
timeout /t 2 /nobreak >nul
echo [OK] Unified hardware bridge started

echo.
echo [STARTING] Schedule Checker...
start "SQUIFM Scheduler" /min cmd /c start_scheduler.bat
timeout /t 1 /nobreak >nul
echo [OK] Schedule Checker started

echo.
echo ========================================
echo    ALL SYSTEMS RUNNING!
echo ========================================
echo.
echo Unified Bridge: Running (python.exe)
echo Schedule Checker: Running
echo.
echo You can now:
echo - Set feeding schedules
echo - Use manual feed
echo - Animation will work automatically
echo.
echo To stop: Close this window or run stop_feeder_system.bat
echo.
pause
