@echo off
echo ========================================
echo TEMPERATURE ^& HUMIDITY SYSTEM CHECK
echo ========================================
echo.

echo [1] Checking if DHT22 Bridge is running...
tasklist | findstr /I "pythonw.exe" >nul
if %errorlevel% equ 0 (
    echo    ✓ Python processes found
    tasklist | findstr /I "pythonw.exe"
) else (
    echo    ✗ No Python processes running
    echo    Run: dht22_bridge_background.bat
)
echo.

echo [2] Checking COM port...
wmic path Win32_SerialPort get DeviceID,Description 2>nul | findstr /I "COM"
if %errorlevel% neq 0 (
    echo    ✗ No COM ports found
    echo    Check if WEMOS is connected via USB
)
echo.

echo [3] Checking lock file...
if exist "..\storage\logs\serial.lock" (
    echo    ⚠ Lock file exists - Servo is active
) else (
    echo    ✓ No lock file - Port is free
)
echo.

echo [4] Checking database table...
php -r "require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); $count = DB::table('sensor_readings')->count(); echo '   ✓ sensor_readings table has ' . $count . ' records' . PHP_EOL;" 2>nul
if %errorlevel% neq 0 (
    echo    ✗ Could not check database
)
echo.

echo [5] Checking API endpoint...
curl -s http://localhost:8000/api/sensor-data/latest >nul 2>&1
if %errorlevel% equ 0 (
    echo    ✓ API endpoint is accessible
    echo    Latest reading:
    curl -s http://localhost:8000/api/sensor-data/latest
) else (
    echo    ✗ API endpoint not accessible
    echo    Make sure Laravel server is running: php artisan serve
)
echo.

echo [6] Checking Temperature page route...
php artisan route:list | findstr /I "temperature-humidity" >nul 2>&1
if %errorlevel% equ 0 (
    echo    ✓ Temperature route exists
) else (
    echo    ✗ Temperature route not found
    echo    Run: php artisan route:clear
)
echo.

echo ========================================
echo QUICK START COMMANDS:
echo ========================================
echo Start DHT22 Bridge:
echo    cd scripts ^&^& dht22_bridge_background.bat
echo.
echo Start Servo Bridge:
echo    cd scripts ^&^& start_servo_bridge.bat
echo.
echo Start Laravel Server:
echo    php artisan serve
echo.
echo Open Temperature Page:
echo    http://localhost:8000/temperature-humidity
echo ========================================
echo.
pause
