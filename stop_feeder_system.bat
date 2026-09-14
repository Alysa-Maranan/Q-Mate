@echo off
title Stop Feeder System
cd /d "%~dp0"
color 0C
echo ========================================
echo    STOPPING FEEDER SYSTEM
echo ========================================
echo.

echo [STOPPING] Unified Hardware Bridge...
REM 1) Kill via PID sa instance file - gumagana KAHIT python.exe ang gamit mo
if exist storage\logs\unified_bridge.instance (
    for /f %%i in (storage\logs\unified_bridge.instance) do taskkill /F /PID %%i >nul 2>&1
)
REM 2) Fallback: pythonw.exe (lumang windowless bridge)
taskkill /F /IM pythonw.exe >nul 2>&1
if "%ERRORLEVEL%"=="0" (
    echo [OK] Servo Bridge stopped
) else (
    echo [INFO] Servo Bridge was not running
)

echo.
echo [STOPPING] Schedule Checker...
taskkill /F /IM php.exe /FI "WINDOWTITLE eq *scheduler*" >nul 2>&1
echo [OK] Schedule Checker stopped

echo.
echo ========================================
echo    ALL SYSTEMS STOPPED
echo ========================================
echo.
pause
