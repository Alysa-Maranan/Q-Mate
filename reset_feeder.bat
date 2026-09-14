@echo off
echo ========================================
echo   COMPLETE RESET - FEEDER SYSTEM
echo ========================================
echo.

echo Step 1: Stopping all processes...
taskkill /F /IM pythonw.exe 2>nul
taskkill /F /IM php.exe 2>nul
timeout /t 2 /nobreak >nul

echo Step 2: Deleting old files...
cd /d "%~dp0storage\logs"
del *.json 2>nul
cd /d "%~dp0"

echo Step 3: Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

echo Step 4: Starting servo bridge...
cd scripts
start /B pythonw servo_bridge.py COM4
cd ..
timeout /t 2 /nobreak >nul

echo Step 5: Starting Laravel server...
start /B php artisan serve
timeout /t 3 /nobreak >nul

echo Step 6: Opening browser...
start http://localhost:8000/feeder

echo.
echo ========================================
echo   RESET COMPLETE!
echo ========================================
echo.
echo INSTRUCTIONS:
echo 1. Scroll down to "Manual Feed" section
echo 2. Set duration: 10 seconds
echo 3. Set cage: 1
echo 4. Click "Feed Now"
echo 5. Wait 3-5 seconds
echo 6. Animation should appear!
echo.
pause
