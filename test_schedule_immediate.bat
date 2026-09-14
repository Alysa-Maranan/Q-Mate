@echo off
echo ========================================
echo   IMMEDIATE SCHEDULE TEST
echo ========================================
echo.

cd /d "%~dp0"

REM Get next minute
for /f "tokens=1-2 delims=:" %%a in ('powershell -Command "Get-Date -Format 'HH:mm'"') do (
    set hour=%%a
    set minute=%%b
)

set /a nextmin=%minute%+1
if %nextmin% LSS 10 set nextmin=0%nextmin%
if %nextmin% GEQ 60 (
    set nextmin=00
    set /a hour=%hour%+1
)

echo Current time: %hour%:%minute%
echo Next check: %hour%:%nextmin%
echo.

echo Creating test schedule for %hour%:%nextmin%...
php artisan tinker --execute="$s = new \App\Models\FeedingSchedule(); $s->time = '%hour%:%nextmin%:00'; $s->days = []; $s->amount = 10; $s->cage_number = 1; $s->enabled = true; $s->status = 'idle'; $s->save(); echo 'Schedule created: ID ' . $s->id;"

echo.
echo Waiting for %hour%:%nextmin%...
echo Keep this window open!
echo.

:loop
for /f "tokens=1-2 delims=:" %%a in ('powershell -Command "Get-Date -Format 'HH:mm'"') do (
    set current=%%a:%%b
)

echo [%current%] Checking...
php artisan feeder:auto-check

if "%current%"=="%hour%:%nextmin%" (
    echo.
    echo TIME MATCHED! Check if servo moved!
    echo.
    pause
    exit
)

timeout /t 10 /nobreak >nul
goto loop
