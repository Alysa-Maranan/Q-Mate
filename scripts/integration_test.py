#!/usr/bin/env python
"""
End-to-end integration test using the UPDATED bridge modules.
Usage: python integration_test.py COM11
"""
import sys, os
sys.path.insert(0, os.path.join(os.path.dirname(__file__)))
import time

import dht22_bridge
import servo_bridge

port = sys.argv[1] if len(sys.argv) > 1 else 'COM11'

print("=" * 50)
print("TEST 1: DHT22 via dht22_bridge.read_sensor()")
print("=" * 50)
response = dht22_bridge.read_sensor(port)
print(f"Raw response:\n{response.strip()}")
temp, hum = dht22_bridge.parse_sensor(response)
if temp is not None and hum is not None:
    print(f"\nPARSED OK -> Temperature: {temp}C | Humidity: {hum}%")
else:
    print("\nPARSE FAILED!")
    sys.exit(1)

print("\n" + "=" * 50)
print("TEST 2: Servo via servo_bridge.do_trigger(3)")
print("=" * 50)
result = servo_bridge.do_trigger(port, 3, cage=1, feed_type='manual')
print(f"Result: {result}")

ok = result.get('success')
print("\n" + ("ALL TESTS PASSED - HARDWARE CONNECTED!" if ok else "SERVO TRIGGER FAILED"))
sys.exit(0 if ok else 1)
