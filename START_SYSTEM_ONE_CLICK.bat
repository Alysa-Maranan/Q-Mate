@echo off
title SQUIFM SYSTEM - ONE CLICK START
REM ===== Auto-elevate to Administrator (para gumana ang firewall rule) =====
net session >nul 2>&1
if %errorlevel% neq 0 (
    powershell -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    exit /b
)
echo ================================================
echo   SQUIFM SYSTEM - ONE CLICK STARTUP
echo   (Walang kailangang i-type. Double click lang!)
echo ================================================
echo.

cd /d "%~dp0"

echo [1/6] Starting Apache + MySQL (XAMPP)...
start "" /min "C:\xampp\xampp_start.exe"
timeout /t 8 /nobreak >nul

echo [2/6] Allowing firewall access (port 80 - para ma-access ng ibang device)...
netsh advfirewall firewall delete rule name="SQUIFM System" >nul 2>&1
netsh advfirewall firewall add rule name="SQUIFM System" dir=in action=allow protocol=TCP localport=80 >nul 2>&1

echo [3/6] Detecting IP address...
set IP=
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    for /f "tokens=* delims= " %%b in ("%%a") do set IP=%%b
)
echo.
echo   ==============================================
echo    I-OPEN SA IBANG DEVICE (phone/laptop):
echo    http://%IP%/CAPSTONE_SQUIFM/public
echo   ==============================================
echo.

echo [4/6] Starting Schedule Auto-Checker (every 60 seconds)...
start "SQUIFM Auto-Checker" /min cmd /c "cd /d %~dp0 && :loop && php artisan feeder:auto-check && timeout /t 60 /nobreak >nul && goto loop"

echo [5/6] Starting Hardware Bridge (COM11 - para sa serial mode kung kakailanganin)...
cd scripts
start "SQUIFM Bridge" /min pythonw unified_bridge.py COM11
cd ..

echo [6/6] Opening system in browser...
timeout /t 3 /nobreak >nul
start http://%IP%/CAPSTONE_SQUIFM/public/feeder

echo.
echo ================================================
echo   SYSTEM IS RUNNING!
echo.
echo   PC (ito):      http://localhost/CAPSTONE_SQUIFM/public
echo   Ibang device:  http://%IP%/CAPSTONE_SQUIFM/public
echo.
echo   Kailangan lang gawin:
echo   1. I-upload ang hardware.ino sa WEMOS (Arduino IDE)
echo      - WiFi: GFiber_DD529 / CA853854
echo      - API:  http://%IP%/CAPSTONE_SQUIFM/public/api
echo   2. Parehong WiFi dapat ang PC at WEMOS
echo.
echo   PARA I-SHUTDOWN: i-close lang itong window
echo   at i-run ang STOP_SYSTEM.bat
echo ================================================
echo.
pause
