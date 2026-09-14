#!/usr/bin/env python
"""
Complete Hardware Bridge for WEMOS D1 R1
Supports: Servo Motor (MG996R), DHT22, HC-SR04 Ultrasonic Sensor

Usage: python hardware_bridge_complete.py [COM_PORT]
Example: python hardware_bridge_complete.py COM3
"""
import sys
import time
import os
import json
import serial
import serial.tools.list_ports

# ============== CONFIGURATION ==============
BASE        = os.path.join(os.path.dirname(__file__), '..', 'storage', 'logs')
CMD_FILE    = os.path.join(BASE, 'servo_command.json')
RESULT_FILE = os.path.join(BASE, 'servo_result.json')
LOCK_FILE   = os.path.join(BASE, 'serial.lock')
ANIM_FILE   = os.path.join(BASE, 'animation_trigger.json')
BAUD        = 115200  # Must match Arduino Serial.begin(115200)

# ============== PORT DETECTION ==============
def find_port():
    """Auto-detect WEMOS D1 R1 serial port"""
    for p in serial.tools.list_ports.comports():
        desc = p.description.lower()
        if any(x in desc for x in ['ch340', 'cp210', 'ftdi', 'wch', 'usb-serial']):
            return p.device
    return None

# ============== SERIAL COMMUNICATION ==============
def open_serial(port):
    """Open serial port without resetting WEMOS"""
    ser = serial.Serial()
    ser.port = port
    ser.baudrate = BAUD
    ser.timeout = 5
    ser.dtr = False  # Prevent WEMOS reboot
    ser.rts = False
    ser.open()
    time.sleep(2)  # Wait for stabilization
    ser.reset_input_buffer()
    return ser

def send_command(ser, command, wait_time=3):
    """Send command and wait for response"""
    ser.write(f'{command}\n'.encode())
    ser.flush()
    
    response_lines = []
    start_time = time.time()
    
    while time.time() - start_time < wait_time:
        if ser.in_waiting:
            try:
                line = ser.readline().decode('utf-8', errors='ignore').strip()
                if line:
                    response_lines.append(line)
            except:
                pass
        else:
            time.sleep(0.1)
    
    return '\n'.join(response_lines)


# ============== SERVO CONTROL ==============
def do_trigger(port, duration, cage=1, feed_type='manual'):
    """Trigger servo for feeding"""
    open(LOCK_FILE, 'w').close()
    try:
        ser = open_serial(port)
        
        # Create animation file for scheduled feeds
        if feed_type == 'scheduled':
            with open(ANIM_FILE, 'w') as f:
                json.dump({
                    'active': True,
                    'duration': duration,
                    'cage': cage,
                    'started_at': time.strftime('%Y-%m-%dT%H:%M:%S+08:00')
                }, f)
        
        # Send OPEN command
        send_command(ser, "OPEN", wait_time=2)
        time.sleep(duration)
        
        # Send CLOSE command
        send_command(ser, "CLOSE", wait_time=2)
        ser.close()
        
        # Remove animation file
        if feed_type == 'scheduled' and os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        
        return {'success': True, 'duration': duration}
    
    except Exception as e:
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        return {'success': False, 'error': str(e)}
    finally:
        if os.path.exists(LOCK_FILE):
            os.remove(LOCK_FILE)


# ============== SENSOR READING ==============
def read_sensors(port):
    """Read DHT22 and Ultrasonic sensors"""
    # Skip if servo is active
    if os.path.exists(LOCK_FILE):
        return None
    
    try:
        ser = open_serial(port)
        
        # Read DHT22 (TEMP command)
        dht_response = send_command(ser, "TEMP", wait_time=5)
        
        # Read Ultrasonic (DISTANCE command)
        ultra_response = send_command(ser, "DISTANCE", wait_time=3)
        
        ser.close()
        
        return {
            'dht22': dht_response,
            'ultrasonic': ultra_response,
            'timestamp': time.strftime('%Y-%m-%dT%H:%M:%S+08:00')
        }
    except Exception as e:
        return None


# ============== MAIN LOOP ==============
def main():
    port = sys.argv[1] if len(sys.argv) > 1 else find_port()
    
    if port is None:
        print("ERROR: No WEMOS found!", flush=True)
        sys.exit(1)
    
    print(f'[{time.strftime("%H:%M:%S")}] Bridge ready ({port})', flush=True)
    
    while True:
        try:
            if os.path.exists(CMD_FILE):
                with open(CMD_FILE, 'r') as f:
                    cmd = json.load(f)
                os.remove(CMD_FILE)
                
                action = cmd.get('action', 'trigger')
                
                if action == 'trigger':
                    duration  = int(cmd.get('duration', 5))
                    cage      = int(cmd.get('cage', 1))
                    feed_type = cmd.get('type', 'manual')
                    
                    print(f'[{time.strftime("%H:%M:%S")}] Feeding: {duration}s (Cage {cage})', flush=True)
                    result = do_trigger(port, duration, cage, feed_type)
                    
                elif action == 'sensor':
                    result = read_sensors(port)
                    result = {'success': result is not None, 'data': result}
                    
                elif action == 'status':
                    try:
                        ser = open_serial(port)
                        status = send_command(ser, "STATUS", wait_time=8)
                        ser.close()
                        result = {'success': True, 'status': status}
                    except Exception as e:
                        result = {'success': False, 'error': str(e)}
                
                else:
                    result = {'success': False, 'error': f'Unknown action: {action}'}
                
                with open(RESULT_FILE, 'w') as f:
                    json.dump({**result, 'port': port, 'timestamp': time.time()}, f)
                
                if result['success']:
                    print(f'[{time.strftime("%H:%M:%S")}] Done ✓', flush=True)
                else:
                    print(f'[{time.strftime("%H:%M:%S")}] Failed ✗', flush=True)
        
        except Exception as e:
            print(f'[{time.strftime("%H:%M:%S")}] Error: {e}', flush=True)
        
        time.sleep(1)

if __name__ == '__main__':
    main()