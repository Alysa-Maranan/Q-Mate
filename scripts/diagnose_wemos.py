#!/usr/bin/env python
"""
Diagnostic tool to test WEMOS D1 R1 connection and commands
"""
import serial
import time
import sys

def test_connection(port, baudrate=115200):
    """Test basic connection to WEMOS"""
    print(f"Testing connection to {port} at {baudrate} baud...")
    
    try:
        ser = serial.Serial(port, baudrate, timeout=2, dsrdtr=False, rtscts=False)
        ser.dtr = False
        ser.rts = False
        time.sleep(3)  # Wait for board to boot
        
        # Clear buffers
        ser.reset_input_buffer()
        ser.reset_output_buffer()
        
        print("OK - Port opened successfully!")
        
        # Test 1: Send newline to see if board responds
        print("\n[Test 1] Sending newline...")
        ser.write(b'\n')
        ser.flush()
        time.sleep(0.5)
        
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f"Response: {repr(response)}")
        else:
            print("(No response)")
        
        # Test 2: Try HELP command
        print("\n[Test 2] Sending 'HELP'...")
        ser.write(b'HELP\n')
        ser.flush()
        time.sleep(1)
        
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f"Response: {repr(response)}")
        else:
            print("(No response)")
        
        # Test 3: Try STATUS command
        print("\n[Test 3] Sending 'STATUS'...")
        ser.write(b'STATUS\n')
        ser.flush()
        time.sleep(1)
        
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f"Response: {repr(response)}")
        else:
            print("(No response)")
        
        # Test 4: Try INFO command
        print("\n[Test 4] Sending 'INFO'...")
        ser.write(b'INFO\n')
        ser.flush()
        time.sleep(1)
        
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f"Response: {repr(response)}")
        else:
            print("(No response)")
        
        # Test 5: Try SENSOR command
        print("\n[Test 5] Sending 'SENSOR'...")
        ser.write(b'SENSOR\n')
        ser.flush()
        time.sleep(2)
        
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f"Response: {repr(response)}")
        else:
            print("(No response)")
        
        # Test 6: Try TRIGGER command
        print("\n[Test 6] Sending 'TRIGGER 2'...")
        ser.write(b'TRIGGER 2\n')
        ser.flush()
        time.sleep(3)
        
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f"Response: {repr(response)}")
        else:
            print("(No response)")
        
        ser.close()
        print("\n=== Diagnostic complete ===")
        
    except Exception as e:
        print(f"ERROR: {e}")

if __name__ == '__main__':
    port = sys.argv[1] if len(sys.argv) > 1 else 'COM4'
    test_connection(port)
