@echo off
echo ========================================
echo SERVO DIAGNOSTIC - FULL CHAIN TEST
echo ========================================
echo.

REM Step 1: Check Python + pyserial
echo [1/6] Testing Python ^& pyserial...
python -c "import serial; print('✓ pyserial OK')" || echo "✗ pyserial missing! pip install pyserial"
echo.

REM Step 2: Test direct Python script execution
echo [2/6] Testing servo_control.py directly...
python scripts\servo_control.py trigger 3 || echo "✗ Python script failed (check COM port)"
echo.

REM Step 3: Test PHP artisan command
echo [3/6] Testing PHP Artisan feeder:check...
php artisan feeder:check --id=1 2^>^&1 || echo "✗ Artisan failed (no schedule ID=1? Create one)"
echo.

REM Step 4: Check recent feeder commands
echo [4/6] Recent FeederCommands:
php artisan tinker --execute="App\Models\FeederCommand::latest()->take(5)->get(['id','type','status','created_at']);"
echo.

REM Step 5: Check Laravel logs (last 20 lines)
echo [5/6] Laravel Log (recent):
tail -n 20 storage/logs/laravel.log || type storage\logs\laravel.log ^| findstr /n ^| sort /r /n ^| more
echo.

REM Step 6: List COM ports (PowerShell)
echo [6/6] Available COM ports:
powershell -c "Get-WmiObject Win32_SerialPort ^| Sort-Object DeviceID ^| Format-Table DeviceID,Name,Description -AutoSize"
echo.

echo ========================================
echo NEXT STEPS:
echo 1. Check [2/6] output ^- if ✗, update .env SERVO_SERIAL_PORT=COMX
echo 2. Check Device Manager for correct COM port
echo 3. Ensure EXTERNAL 5V power to servo
echo 4. Arduino IDE Serial Monitor: TRIGGER 5
echo ========================================
pause

