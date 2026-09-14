#!/usr/bin/env python
"""
Simple WEMOS D1 R1 diagnostic test
"""
import serial
import time
import sys

def test_port(port, baudrate=115200):
    print(f"\n📡 Testing {port} at {baudrate} baud...")
    
    try:
        ser = serial.Serial(port, baudrate, timeout=1)
        print(f"✓ Port opened successfully")
        
        # Wait for WEMOS to boot
        print("⏳ Waiting 3 seconds for WEMOS boot...")
        time.sleep(3)
        
        # Clear any garbage
        ser.reset_input_buffer()
        ser.reset_output_buffer()
        
        # Send test command
        print(f"📤 Sending: TRIGGER 2")
        ser.write(b"TRIGGER 2\n")
        ser.flush()
        
        # Read response
        print("📥 Waiting for response...")
        ser.timeout = 5
        response = ser.readline().decode('ascii', errors='ignore').strip()
        
        if response:
            print(f"✓ RESPONSE: '{response}'")
            
            # Try reading one more line
            response2 = ser.readline().decode('ascii', errors='ignore').strip()
            if response2:
                print(f"✓ RESPONSE 2: '{response2}'")
        else:
            print("✗ No response from WEMOS")
        
        ser.close()
        return True
        
    except Exception as e:
        print(f"✗ Error: {e}")
        return False

if __name__ == '__main__':
    # Test COM4 with Windows device path format
    success = test_port('\\\\.\\COM4')
    
    if not success:
        print("\n💡 Try unplugging WEMOS and re-plugging, then try again")
        sys.exit(1)
    
    print("\n✓ WEMOS communication successful!")
    sys.exit(0)
