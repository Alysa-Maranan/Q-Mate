@echo off
cd /d "%~dp0"
start /B pythonw unified_bridge.py
echo Unified servo and sensor bridge started using SERVO_SERIAL_PORT
