@echo off
echo ========================================
echo   WEMOS Unified Bridge - All In One
echo   Servo + DHT22 + Food Level (%)
echo ========================================
echo.
echo Starting unified bridge...
echo Press Ctrl+C to stop.
echo.

cd /d "%~dp0"
python scripts\unified_bridge.py COM11

pause
