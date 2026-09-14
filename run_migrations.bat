@echo off
echo Running database migrations...
cd /d "c:\xampp\htdocs\CAPSTONE SQUIFM"

echo.
echo === Running migrations ===
php artisan migrate --force

echo.
echo === Checking database status ===
php test_db.php

echo.
echo === Done ===
pause