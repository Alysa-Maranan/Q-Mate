@echo off
echo ========================================
echo   YOUR LOCAL IP ADDRESS
echo ========================================
echo.
ipconfig | findstr /i "IPv4"
echo.
echo ========================================
echo Copy the IPv4 Address above
echo ========================================
pause
