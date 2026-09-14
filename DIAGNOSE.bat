@echo off
echo ========================================
echo   SYSTEM DIAGNOSTIC CHECK
echo ========================================
echo.

cd /d "%~dp0"

echo [1/7] Checking Python...
python --version 2>nul
if errorlevel 1 (
    echo    ERROR: Python not found!
    echo    Install Python from: https://www.python.org/downloads/
) else (
    echo    OK: Python is installed
)
echo.

echo [2/7] Checking PySerial...
python -c "import serial; print('   OK: PySerial version', serial.__version__)" 2>nul
if errorlevel 1 (
    echo    ERROR: PySerial not installed!
    echo    Run: pip install pyserial
)
echo.

echo [3/7] Checking Servo Bridge...
tasklist | findstr pythonw >nul
if errorlevel 1 (
    echo    ERROR: Servo bridge NOT running!
    echo    Run: scripts\start_servo_bridge.bat
) else (
    echo    OK: Servo bridge is running
)
echo.

echo [4/7] Checking Laravel Server...
tasklist | findstr php >nul
if errorlevel 1 (
    echo    ERROR: Laravel server NOT running!
    echo    Run: php artisan serve
) else (
    echo    OK: Laravel server is running
)
echo.

echo [5/7] Checking Database Connection...
php artisan tinker --execute="try { \DB::connection()->getPdo(); echo '   OK: Database connected'; } catch (\Exception $e) { echo '   ERROR: ' . $e->getMessage(); }" 2>nul
echo.

echo [6/7] Checking Schedules...
php artisan tinker --execute="$count = \App\Models\FeedingSchedule::where('enabled', true)->count(); echo '   Found ' . $count . ' enabled schedule(s)';" 2>nul
echo.

echo [7/7] Checking Files...
if exist "storage\logs\servo_result.json" (
    echo    OK: servo_result.json exists
) else (
    echo    WARNING: servo_result.json not found
)
if exist "storage\logs\animation_trigger.json" (
    echo    ACTIVE: animation_trigger.json exists
) else (
    echo    OK: No active animation
)
echo.

echo ========================================
echo   DIAGNOSTIC COMPLETE
echo ========================================
echo.
echo If you see any ERRORS above, fix them first!
echo Then run: START_EVERYTHING.bat
echo.
pause
