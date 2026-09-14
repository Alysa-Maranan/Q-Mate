@echo off
echo ========================================
echo   STOPPING AUTOMATIC FEEDER
echo ========================================
echo.

REM Stop the scheduled task
schtasks /end /tn "QuailFeederAutoCheck"
schtasks /delete /tn "QuailFeederAutoCheck" /f

REM Stop servo bridge
taskkill /F /IM pythonw.exe 2>nul

echo.
echo ========================================
echo   AUTOMATIC FEEDER STOPPED
echo ========================================
echo.
echo The automatic feeder has been stopped.
echo To start it again, run: setup_auto_feeder.bat
echo.
pause
