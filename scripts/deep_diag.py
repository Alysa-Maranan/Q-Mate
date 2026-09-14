#!/usr/bin/env python
"""
Deep diagnostic: try multiple baud rates, listen passively AND actively.
Usage: python deep_diag.py COM11
"""
import sys, time, serial

port = sys.argv[1] if len(sys.argv) > 1 else 'COM11'
BAUDS = [115200, 9600, 57600, 74880]

def clean(s):
    return ''.join(ch if 32 <= ord(ch) < 127 else '.' for ch in s)

for baud in BAUDS:
    print(f"\n{'='*50}\nBaud {baud}:\n{'='*50}")
    try:
        ser = serial.Serial()
        ser.port = port
        ser.baudrate = baud
        ser.timeout = 1
        ser.dtr = False
        ser.rts = False
        ser.open()
    except Exception as e:
        print(f"  Open failed: {e}")
        continue

    # Phase 1: passive listen 4s (catch boot msg / ready msg)
    ser.reset_input_buffer()
    print("  [passive listen 4s]")
    t0 = time.time()
    got = b""
    while time.time() - t0 < 4:
        if ser.in_waiting:
            got += ser.read(ser.in_waiting)
        time.sleep(0.05)
    if got:
        print(f"  PASSIVE: {clean(got.decode('utf-8', 'ignore'))[:200]}")
    else:
        print("  PASSIVE: (nothing)")

    # Phase 2: active - send STATUS, read 4s
    ser.reset_input_buffer()
    ser.write(b'STATUS\n')
    ser.flush()
    print("  [sent STATUS, listening 4s]")
    t0 = time.time()
    got = b""
    while time.time() - t0 < 4:
        if ser.in_waiting:
            got += ser.read(ser.in_waiting)
        time.sleep(0.05)
    if got:
        print(f"  ACTIVE: {clean(got.decode('utf-8', 'ignore'))[:200]}")
    else:
        print("  ACTIVE: (nothing)")
    ser.close()

print("\nDone.")
