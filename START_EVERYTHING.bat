@echo off
echo ========================================
echo   COMPLETE FEEDER SYSTEM STARTUP
echo ========================================
echo.

cd /d "%~dp0"

echo Step 1: Stopping old processes...
taskkill /F /IM pythonw.exe 2>nul
taskkill /F /IM php.exe 2>nul
timeout /t 2 /nobreak >nul

echo Step 2: Cleaning old files...
del storage\logs\*.json 2>nul

echo Step 3: Starting Unified Bridge (WEMOS: servo + DHT22 + food level)...
cd scripts
start "Unified Bridge" pythonw unified_bridge.py COM11
cd ..
timeout /t 3 /nobreak >nul

echo Step 4: Verifying bridges are running...
tasklist | findstr pythonw >nul
if errorlevel 1 (
    echo ERROR: Bridge failed to start!
    echo Check if Python is installed: python --version
    pause
    exit /b 1
) else (
    echo SUCCESS: Unified Bridge is running!
)
echo   - Unified Bridge (WEMOS COM11): servo + temperature + ultrasonic food level

echo Step 5: Starting Laravel Server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul

echo Step 6: Starting Auto-Checker...
start "Auto Checker" cmd /k "cd /d %~dp0 && :loop && echo [%%date%% %%time%%] Checking schedules... && php artisan feeder:auto-check && timeout /t 60 /nobreak >nul && goto loop"
timeout /t 2 /nobreak >nul

echo Step 7: Opening Browser...
timeout /t 2 /nobreak >nul
start http://localhost:8000/feeder
timeout /t 2 /nobreak >nul
start http://localhost:8000/dashboard

echo.
echo ========================================
echo   ALL SYSTEMS RUNNING!
echo ========================================
echo.
echo You should now see these running:
echo 1. Unified Bridge (WEMOS COM11): servo + temperature + food level
echo 2. Laravel Server
echo 3. Auto Checker (checking every minute)
echo.
echo Plus 2 browser tabs:
echo - Feeder page
echo - Dashboard
echo.
echo Food level inu-update tuwing 5 seconds (Level: XX%% - puro %, walang cm)
echo.
echo NOW ADD A SCHEDULE:
echo 1. Go to feeder page
echo 2. Click "Schedule" tab
echo 3. Set time: 1-2 minutes from now
echo 4. Click "Add Schedule"
echo 5. Wait and watch!
echo.
pause
