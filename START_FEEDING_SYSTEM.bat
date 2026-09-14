@echo off
title SQUIFM - Complete Feeding System
color 0A

echo ========================================
echo   SQUIFM COMPLETE FEEDING SYSTEM
echo ========================================
echo.
echo Starting all required services...
echo.

REM Check if servo_bridge.py is already running
tasklist /FI "IMAGENAME eq pythonw.exe" 2>NUL | find /I /N "pythonw.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] servo_bridge.py is already running
) else (
    echo [STARTING] servo_bridge.py...
    cd scripts
    start /B pythonw servo_bridge.py COM4
    cd ..
    timeout /t 2 /nobreak > nul
    echo [OK] servo_bridge.py started
)

echo.
echo ========================================
echo   SYSTEM READY
echo ========================================
echo.
echo Now starting automatic feeding checker...
echo Checking every 3 SECONDS for minimal delay.
echo.
echo Press CTRL+C to stop
echo.
echo ========================================
echo.

:loop
php artisan feeder:check-automatic
timeout /t 3 /nobreak > nul
goto loop
