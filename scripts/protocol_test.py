#!/usr/bin/env python
"""
Test if current firmware supports TRIGGER/SENSOR at 9600 baud.
Usage: python protocol_test.py COM11
"""
import sys, time, serial

port = sys.argv[1] if len(sys.argv) > 1 else 'COM11'
BAUD = 9600

def clean(s):
    return ''.join(ch if 32 <= ord(ch) < 127 else '.' for ch in s)

def send_and_read(ser, cmd, wait=6):
    ser.reset_input_buffer()
    ser.write((cmd + '\n').encode())
    ser.flush()
    got = b""
    t0 = time.time()
    while time.time() - t0 < wait:
        if ser.in_waiting:
            got += ser.read(ser.in_waiting)
        time.sleep(0.05)
    return clean(got.decode('utf-8', 'ignore')).strip()

ser = serial.Serial()
ser.port = port
ser.baudrate = BAUD
ser.timeout = 1
ser.dtr = False
ser.rts = False
ser.open()
print(f"Opened {port} @ {BAUD}")
time.sleep(3)
ser.reset_input_buffer()

print("\n--- TEST 1: SENSOR command ---")
print("Response:", send_and_read(ser, 'SENSOR', wait=8) or "(nothing)")

print("\n--- TEST 2: TEMP command ---")
print("Response:", send_and_read(ser, 'TEMP', wait=5) or "(nothing)")

print("\n--- TEST 3: TRIGGER 3 (servo will move!) ---")
print("Response:", send_and_read(ser, 'TRIGGER 3', wait=12) or "(nothing)")

print("\n--- TEST 4: STATUS after trigger ---")
print("Response:", send_and_read(ser, 'STATUS', wait=6) or "(nothing)")

ser.close()
print("\nDone.")
