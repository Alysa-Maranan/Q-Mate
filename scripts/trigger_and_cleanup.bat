@echo off
"python" "C:\xampp\htdocs\CAPSTONE SQUIFM\scripts/servo_dht22_web.py" trigger 10 COM4
timeout /t 10 /nobreak > nul
del /f /q "C:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs/animation_trigger.json" 2>nul
