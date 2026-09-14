@echo off
echo ========================================
echo SQUIFM Servo Motor Setup & Test Script
echo ========================================
echo.

echo [1] Checking Python installation...
py --version
if %errorlevel% neq 0 (
    echo ERROR: Python not found! Please install Python from python.org
    pause
    exit /b 1
)
echo.

echo [2] Checking pyserial installation...
py -m pip show pyserial
if %errorlevel% neq 0 (
    echo pyserial is NOT installed. Installing now...
    py -m pip install pyserial
    echo.
)
echo.

echo [3] Available COM ports (ESP32 should be connected via USB):
echo.
py -m serial.tools.list_ports
echo.
echo NOTE: Look for USB Serial Device (COMX) - this is your ESP32
echo.

echo [4] Testing servo motor...
echo Make sure your ESP32 is connected via USB!
echo.
set /p PORT="Enter COM port (e.g., COM4): "

echo.
echo Running servo test for 3 seconds...
echo.
py "%~dp0scripts\servo_control.py" trigger 3 %PORT%

echo.
echo ========================================
echo Test complete!
echo.
echo If the servo motor moved, it is working!
echo If not, check:
echo   1. Is ESP32 connected to USB?
echo   2. Is the correct COM port selected?
echo   3. Is the Arduino sketch uploaded to ESP32?
echo ========================================

pause
