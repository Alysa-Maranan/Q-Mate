@echo off
cd /d "%~dp0"
start /B pythonw dht22_bridge.py COM4
