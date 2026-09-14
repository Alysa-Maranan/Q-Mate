@echo off
echo ========================================
echo   OPENING TEST PAGES
echo ========================================
echo.

REM Start Laravel server if not running
tasklist /FI "IMAGENAME eq php.exe" 2>NUL | find /I /N "php.exe">NUL
if "%ERRORLEVEL%"=="1" (
    echo Starting Laravel server...
    start /B php artisan serve
    timeout /t 3 /nobreak >nul
)

echo Opening test pages in browser...
echo.

REM Open test page
start http://localhost:8000/test-feeding

REM Wait 2 seconds
timeout /t 2 /nobreak >nul

REM Open feeder page
start http://localhost:8000/feeder

REM Wait 2 seconds
timeout /t 2 /nobreak >nul

REM Open dashboard
start http://localhost:8000/dashboard

echo.
echo ========================================
echo   ALL PAGES OPENED!
echo ========================================
echo.
echo INSTRUCTIONS:
echo 1. In TEST page - Click "Start Feeding"
echo 2. Switch to FEEDER page - Animation should appear
echo 3. Switch to DASHBOARD - Notification should appear
echo.
pause
