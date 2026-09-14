@echo off
title SQUIFM SYSTEM - STOP
echo Stopping SQUIFM System...
taskkill /F /IM pythonw.exe >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq SQUIFM Auto-Checker*" >nul 2>&1
taskkill /F /FI "WINDOWTITLE eq SQUIFM Bridge*" >nul 2>&1
start "" /min "C:\xampp\xampp_stop.exe"
echo Done! Apache, MySQL at bridges ay napatay na.
pause
