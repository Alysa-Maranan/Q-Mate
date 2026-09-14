@echo off
echo ========================================
echo   ADDING FIREWALL RULE FOR PORT 8000
echo ========================================
echo.
echo This will allow phone access to Laravel...
echo.
netsh advfirewall firewall add rule name="Laravel Server Port 8000" dir=in action=allow protocol=TCP localport=8000
echo.
echo ========================================
echo   FIREWALL RULE ADDED!
echo ========================================
echo.
echo Now you can access from phone:
echo http://192.168.1.27:8000
echo or
echo http://192.168.1.50:8000
echo.
pause
