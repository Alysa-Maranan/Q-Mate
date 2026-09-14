@echo off
title Animation Diagnostic Test
color 0E
echo ========================================
echo    ANIMATION DIAGNOSTIC TEST
echo ========================================
echo.

echo [1] Checking if servo bridge is running...
tasklist /FI "IMAGENAME eq pythonw.exe" 2>NUL | find /I /N "pythonw.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Servo Bridge is RUNNING
) else (
    echo [ERROR] Servo Bridge is NOT running!
    echo Please run: start_feeder_system.bat
    pause
    exit /b 1
)

echo.
echo [2] Checking animation file...
if exist "storage\logs\animation_trigger.json" (
    echo [FOUND] animation_trigger.json exists
    type storage\logs\animation_trigger.json
) else (
    echo [OK] No animation file (normal when not feeding)
)

echo.
echo [3] Creating test animation file...
echo {"active":true,"duration":5,"cage":1,"started_at":"2025-01-15T12:00:00+08:00"} > storage\logs\animation_trigger.json
echo [OK] Test file created

echo.
echo [4] Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo.
echo [5] Checking if file still exists...
if exist "storage\logs\animation_trigger.json" (
    echo [WARNING] File still exists - this is NORMAL for test
    echo In real feeding, servo_bridge will delete this after duration
) else (
    echo [OK] File was deleted
)

echo.
echo [6] Cleaning up test file...
if exist "storage\logs\animation_trigger.json" (
    del storage\logs\animation_trigger.json
    echo [OK] Test file deleted
)

echo.
echo ========================================
echo    DIAGNOSTIC COMPLETE
echo ========================================
echo.
echo To test real feeding:
echo 1. Open browser to Feeder Management
echo 2. Open browser console (F12)
echo 3. Click Manual Feed
echo 4. Watch console logs
echo.
echo Expected console output:
echo [Manual Feed] Starting animation: X seconds, cage Y
echo [Animation] Started - polling for completion...
echo [Animation] Poll #1: {active: true, ...}
echo [Animation] Poll #2: {active: false}
echo [Animation] File deleted - closing animation
echo [Animation] Hiding in 1 second...
echo [Animation] Closed - reloading page...
echo.
pause
