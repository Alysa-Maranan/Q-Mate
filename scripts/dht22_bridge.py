#!/usr/bin/env python
"""
DHT22 Bridge Script - Reads temperature & humidity from WEMOS and posts to Laravel API
"""
import sys
import time
import json
import re
import os

LOCK_FILE = os.path.join(os.path.dirname(__file__), '..', 'storage', 'logs', 'serial.lock')

try:
    import serial
    import serial.tools.list_ports
except ImportError:
    print("ERROR: pyserial not installed. Run: pip install pyserial")
    sys.exit(1)

import urllib.request
import urllib.error

def configured_value(name, default):
    value = os.getenv(name)
    if value:
        return value

    laravel_env = os.path.join(os.path.dirname(__file__), '..', '.env')
    if os.path.exists(laravel_env):
        with open(laravel_env, 'r', encoding='utf-8') as env_file:
            for line in env_file:
                line = line.strip()
                if line.startswith(f'{name}='):
                    return line.split('=', 1)[1].strip().strip('"\'')
    return default

API_BASE_URL  = configured_value(
    "HARDWARE_API_BASE_URL",
    "http://127.0.0.1:8000"
).rstrip("/")
API_URL       = f"{API_BASE_URL}/api/sensor-data"
BAUD_RATE     = 115200  # WEMOS firmware runs at 115200
READ_INTERVAL = 5  # seconds between readings (real-time)

def find_port():
    for port in serial.tools.list_ports.comports():
        if any(x in port.description.lower() for x in ['ch340', 'cp210', 'ftdi', 'wch', 'usb-serial']):
            return port.device
    return "COM4"

def read_sensor(port):
    """Open port, read sensor, close port immediately."""
    try:
        ser = serial.Serial()
        ser.port = port
        ser.baudrate = BAUD_RATE
        ser.timeout = 5
        ser.dtr = False  # avoid Wemos reboot on open
        ser.rts = False
        ser.open()

        # Short stabilization wait
        time.sleep(2)
        ser.reset_input_buffer()

        # Send TEMP command (new Arduino code)
        ser.write(b'TEMP\n')
        ser.flush()

        response = ""
        deadline = time.time() + 8
        while time.time() < deadline:
            if ser.in_waiting:
                line = ser.readline().decode('utf-8', errors='ignore').strip()
                if line:
                    response += line + "\n"
                    if 'Temperature' in line:
                        break
            else:
                time.sleep(0.1)

        ser.close()
        return response
    except Exception as e:
        print(f"Serial error: {e}")
        return ""

def parse_sensor(response):
    temp = hum = None
    m = re.search(r'Temperature:\s*([\d.]+)', response)
    if m:
        temp = float(m.group(1))
    m = re.search(r'Humidity:\s*([\d.]+)', response)
    if m:
        hum = float(m.group(1))
    return temp, hum

def post_to_api(temperature, humidity):
    payload = json.dumps({"temperature": temperature, "humidity": humidity}).encode()
    req = urllib.request.Request(
        API_URL, data=payload,
        headers={"Content-Type": "application/json"}, method="POST"
    )
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
            return json.loads(resp.read().decode())
    except Exception as e:
        return {"success": False, "error": str(e)}

def main():
    port = sys.argv[1] if len(sys.argv) > 1 else find_port()
    print(f"DHT22 Bridge starting - port={port}, interval={READ_INTERVAL}s")
    print(f"Posting to: {API_URL}")
    print("Press Ctrl+C to stop.\n")

    while True:
        try:
            # Skip if servo is currently using the port
            if os.path.exists(LOCK_FILE):
                print("Servo active, waiting...")
                time.sleep(5)
                continue

            response = read_sensor(port)
            if response.strip():
                print(f"Raw: {response.strip()}")

            if not response.strip() or "ERR" in response:
                print("WARNING: Sensor read failed\n")
                time.sleep(READ_INTERVAL)
                continue

            temp, hum = parse_sensor(response)
            if temp is None or hum is None:
                print("WARNING: Could not parse data\n")
                time.sleep(READ_INTERVAL)
                continue

            print(f"Temp: {temp}C | Humidity: {hum}%")
            result = post_to_api(temp, hum)
            if result.get("success"):
                print(f"Saved (id={result.get('id')})\n")
            else:
                print(f"API error: {result.get('error')}\n")

        except KeyboardInterrupt:
            print("\nStopped.")
            sys.exit(0)
        except Exception as e:
            print(f"Error: {e}")

        time.sleep(READ_INTERVAL)

if __name__ == '__main__':
    main()
