@echo off
cd /d C:\xampp\htdocs\CAPSTONE SQUIFM

start /B pythonw scripts\unified_bridge.py COM11

echo Hardware bridge started silently!
echo ============================================
echo Servo + DHT22 + Ultrasonic (Food Level %%) = Running
echo Check dashboard for real-time data.
echo ============================================
timeout /t 3