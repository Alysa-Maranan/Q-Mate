@echo off
echo ========================================
echo   MANUAL SERVO TRIGGER TEST
echo ========================================
echo.

echo Creating servo command file...
echo {"action":"trigger","duration":20,"port":"COM4","cage":1,"type":"scheduled"} > storage\logs\servo_command.json

echo.
echo Command file created!
echo.
echo Contents:
type storage\logs\servo_command.json
echo.
echo.

echo If servo_bridge.py is running, servo should trigger now!
echo Check if animation appears on the website.
echo.

pause
