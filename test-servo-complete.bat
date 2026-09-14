@echo off
echo ========================================
echo SERVO FULL TEST - ONE CLICK FIX CHECK
echo ========================================
echo.

REM Kill Arduino locks
taskkill /f /im "Arduino IDE.exe" 2>nul
taskkill /f /im arduino-cli.exe 2>nul
timeout /t 2 /nobreak >nul

REM Test port connection
echo [TEST 1] Python & pyserial OK? 
py -m pip show pyserial

echo [TEST 2] COM ports:
py -m serial.tools.list_ports

echo [TEST 3] Servo trigger test (3 sec):
py scripts/servo_control.py trigger 3 COM4

echo [TEST 4] Sensor read:
py scripts/servo_dht22_web
