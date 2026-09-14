@echo off
cd /d "c:\xampp\htdocs\CAPSTONE SQUIFM"
c:\xampp\php\php.exe artisan schedule:run >> "storage\logs\scheduler.log" 2>&1
