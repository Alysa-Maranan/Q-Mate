@echo off
echo ========================================
echo   CHECKING SERVO BRIDGE STATUS
echo ========================================
echo.

tasklist /FI "IMAGENAME eq pythonw.exe" 2>NUL | find /I /N "pythonw.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] servo_bridge.py is RUNNING
    echo.
    tasklist | find "pythonw"
) else (
    echo [ERROR] servo_bridge.py is NOT RUNNING!
    echo.
    echo To start it, run:
    echo   START_FEEDING_SYSTEM.bat
    echo.
    echo Or manually:
    echo   cd scripts
    echo   start pythonw servo_bridge.py COM4
)

echo.
echo ========================================
pause
