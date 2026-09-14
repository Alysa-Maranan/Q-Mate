#!/usr/bin/env python
"""
Quick hardware check: WEMOS on given COM port (default: auto-detect).
Tests STATUS command -> returns servo position + DHT22 reading.
"""
import sys, time, serial, serial.tools.list_ports

def find_port():
    for p in serial.tools.list_ports.comports():
        if any(x in p.description.lower() for x in ['ch340', 'cp210', 'ftdi', 'wch', 'usb-serial']):
            return p.device
    return None

port = sys.argv[1] if len(sys.argv) > 1 else find_port()
if not port:
    print("NO WEMOS FOUND - check USB cable!")
    sys.exit(1)

print(f"Testing {port} @ 9600 baud...")
ser = serial.Serial()
ser.port = port
ser.baudrate = 9600
ser.timeout = 5
ser.dtr = False   # avoid WEMOS reboot on open
ser.rts = False
try:
    ser.open()
except Exception as e:
    print(f"FAILED to open {port}: {e}")
    sys.exit(1)

print("Port opened. Waiting 3s for boot...")
time.sleep(3)
ser.reset_input_buffer()

print("Sending STATUS...")
ser.write(b'STATUS\n')
ser.flush()

response = ""
deadline = time.time() + 12
while time.time() < deadline:
    if ser.in_waiting:
        line = ser.readline().decode('utf-8', errors='ignore').strip()
        if line:
            print(f"  << {line}")
            response += line + "\n"
            if "Temperature" in line and "Humidity" in line:
                break
    else:
        time.sleep(0.1)

ser.close()

ok = "Temperature" in response and "Humidity" in response
print("\nRESULT:", "HARDWARE OK - Servo & DHT22 responding!" if ok else "NO VALID RESPONSE - check wiring/firmware")
sys.exit(0 if ok else 2)
