@echo off
title SQUIFM Hardware System
cd /d C:\xampp\htdocs\CAPSTONE SQUIFM

echo ============================================
echo   SQUIFM Hardware System Starting...
echo ============================================
echo.

start /B pythonw scripts\unified_bridge.py

echo.
echo ============================================
echo   Hardware bridge started silently!
echo ============================================
echo.
echo Servo + DHT22 + Ultrasonic = Running
echo Check dashboard for real-time data.
echo ============================================
timeout /t 3