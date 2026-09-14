@echo off
title SQUIFM Hardware System
cd /d "%~dp0"

echo ============================================
echo   SQUIFM Hardware System Starting...
echo ============================================
echo.

start "Unified Bridge (Servo + DHT22 + Food Level)" cmd /k "python scripts\unified_bridge.py COM11"

echo.
echo ============================================
echo   Unified bridge started!
echo ============================================
echo.
echo Unified Bridge: Servo motor + DHT22 + Ultrasonic Food Level (%)
echo.
echo Press any key to close this window...
echo ============================================
pause >nul