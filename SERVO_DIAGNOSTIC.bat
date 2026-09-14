@echo off
title SERVO DIAGNOSTIC - Complete Test
color 0B
echo ========================================
echo    SERVO DIAGNOSTIC - COMPLETE TEST
echo ========================================
echo.

REM Step 1: Check servo bridge
echo [STEP 1] Checking if servo bridge is running...
tasklist /FI "IMAGENAME eq pythonw.exe" 2>NUL | find /I /N "pythonw.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Servo Bridge is RUNNING
    echo.
    tasklist /FI "IMAGENAME eq pythonw.exe"
) else (
    echo [ERROR] Servo Bridge is NOT running!
    echo.
    echo SOLUTION: Run start_feeder_system.bat
    echo.
    choice /C YN /M "Do you want to start servo bridge now"
    if errorlevel 2 goto :skip_start
    if errorlevel 1 (
        echo Starting servo bridge...
        cd scripts
        start /B pythonw servo_bridge.py COM4
        cd ..
        timeout /t 2 /nobreak >nul
        echo [OK] Servo bridge started
    )
)
:skip_start

echo.
echo ========================================
echo [STEP 2] Checking COM port...
echo ========================================
echo.
echo Opening Device Manager to check COM port...
echo Look for "Ports (COM ^& LPT)" section
echo Find your Arduino/WEMOS device
echo Note the COM port number (e.g., COM4, COM3, etc.)
echo.
pause
start devmgmt.msc
echo.
set /p COMPORT="Enter your COM port (e.g., COM4): "
echo You entered: %COMPORT%
echo.

echo ========================================
echo [STEP 3] Testing servo_command.json creation...
echo ========================================
echo.
echo Creating test command file...
echo {"action":"trigger","duration":5,"port":"%COMPORT%","cage":1,"type":"manual"} > storage\logs\servo_command.json
echo [OK] Command file created
echo.
type storage\logs\servo_command.json
echo.

echo ========================================
echo [STEP 4] Waiting for servo bridge to process...
echo ========================================
echo.
echo Servo bridge should:
echo 1. Detect servo_command.json
echo 2. Delete servo_command.json
echo 3. Create animation_trigger.json
echo 4. Send command to Arduino
echo 5. Wait 5 seconds
echo 6. Delete animation_trigger.json
echo 7. Create servo_result.json
echo.
echo Waiting 10 seconds...
timeout /t 10 /nobreak

echo.
echo ========================================
echo [STEP 5] Checking results...
echo ========================================
echo.

echo Checking if command file was processed...
if exist "storage\logs\servo_command.json" (
    echo [ERROR] Command file still exists!
    echo This means servo bridge did NOT process it
    echo.
    echo POSSIBLE CAUSES:
    echo 1. Servo bridge is not running
    echo 2. Servo bridge crashed
    echo 3. Wrong COM port
    echo 4. Arduino not connected
    echo.
    type storage\logs\servo_command.json
) else (
    echo [OK] Command file was processed (deleted)
)

echo.
echo Checking if result file was created...
if exist "storage\logs\servo_result.json" (
    echo [OK] Result file exists
    echo.
    type storage\logs\servo_result.json
    echo.
) else (
    echo [WARNING] No result file found
)

echo.
echo Checking if animation file exists...
if exist "storage\logs\animation_trigger.json" (
    echo [WARNING] Animation file still exists
    echo This means servo is still running OR there was an error
    echo.
    type storage\logs\animation_trigger.json
) else (
    echo [OK] Animation file was cleaned up
)

echo.
echo ========================================
echo [STEP 6] Testing direct Arduino communication...
echo ========================================
echo.
echo This will test if we can communicate with Arduino directly
echo using Python script (bypassing servo bridge)
echo.
pause

python scripts\servo_control.py trigger 3 %COMPORT%
if "%ERRORLEVEL%"=="0" (
    echo.
    echo [SUCCESS] Direct communication works!
    echo Arduino responded correctly
    echo.
    echo This means:
    echo - Arduino is connected
    echo - COM port is correct
    echo - Python can communicate with Arduino
    echo.
    echo If servo bridge doesn't work, the problem is with servo_bridge.py
) else (
    echo.
    echo [ERROR] Direct communication failed!
    echo.
    echo POSSIBLE CAUSES:
    echo 1. Wrong COM port
    echo 2. Arduino not connected
    echo 3. Arduino not programmed
    echo 4. USB cable issue
    echo 5. Python pyserial not installed
    echo.
    echo SOLUTIONS:
    echo 1. Check COM port in Device Manager
    echo 2. Check USB connection
    echo 3. Upload Arduino sketch again
    echo 4. Install pyserial: pip install pyserial
)

echo.
echo ========================================
echo [STEP 7] Summary
echo ========================================
echo.

echo Servo Bridge Status:
tasklist /FI "IMAGENAME eq pythonw.exe" 2>NUL | find /I /N "pythonw.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Running
) else (
    echo [ERROR] Not running
)

echo.
echo Files Status:
if exist "storage\logs\servo_command.json" (
    echo [ERROR] servo_command.json still exists
) else (
    echo [OK] servo_command.json processed
)

if exist "storage\logs\servo_result.json" (
    echo [OK] servo_result.json created
) else (
    echo [WARNING] servo_result.json not found
)

if exist "storage\logs\animation_trigger.json" (
    echo [WARNING] animation_trigger.json still exists
) else (
    echo [OK] animation_trigger.json cleaned up
)

echo.
echo ========================================
echo RECOMMENDATIONS
echo ========================================
echo.

if exist "storage\logs\servo_command.json" (
    echo 1. Servo bridge is NOT processing commands
    echo    → Check if servo bridge is running
    echo    → Check servo bridge output for errors
    echo    → Restart servo bridge
    echo.
)

python scripts\servo_control.py trigger 1 %COMPORT% >nul 2>&1
if "%ERRORLEVEL%" NEQ "0" (
    echo 2. Direct Arduino communication FAILED
    echo    → Check COM port: %COMPORT%
    echo    → Check USB connection
    echo    → Check Arduino is programmed
    echo    → Install pyserial: pip install pyserial
    echo.
)

tasklist /FI "IMAGENAME eq pythonw.exe" 2>NUL | find /I /N "pythonw.exe">NUL
if "%ERRORLEVEL%" NEQ "0" (
    echo 3. Servo bridge is NOT running
    echo    → Run: start_feeder_system.bat
    echo    → Or: cd scripts ^&^& start /B pythonw servo_bridge.py %COMPORT%
    echo.
)

echo.
echo ========================================
echo NEXT STEPS
echo ========================================
echo.
echo If everything is OK:
echo 1. Go to Feeder Management in browser
echo 2. Try Manual Feed
echo 3. Check browser console (F12)
echo 4. Servo should move
echo.
echo If servo doesn't move:
echo 1. Check this diagnostic output
echo 2. Fix any [ERROR] or [WARNING] issues
echo 3. Run this diagnostic again
echo.
pause
