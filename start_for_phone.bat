@echo off
echo ========================================
echo   STARTING LARAVEL FOR PHONE ACCESS
echo ========================================
echo.
echo Getting your IP address...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do set IP=%%a
set IP=%IP:~1%
echo.
echo ========================================
echo   YOUR IP ADDRESS: %IP%
echo ========================================
echo.
echo Starting Laravel server...
echo.
echo OPEN THIS ON YOUR PHONE:
echo http://%IP%:8000
echo.
echo ========================================
echo Make sure your phone is connected to the SAME WiFi!
echo ========================================
echo.
cd /d "c:\xampp\htdocs\CAPSTONE SQUIFM"
php artisan serve --host=0.0.0.0 --port=8000
