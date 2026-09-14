#!/usr/bin/env python
"""
Combined Servo + DHT22 Control Script for WEMOS D1 R1
Communicates via USB Serial and returns both servo control and sensor data
"""
import sys
import time
import os
import json
import traceback

# Force stdout/stderr to be unbuffered (Python 3.7+ compatible)
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(line_buffering=True)
if hasattr(sys.stderr, 'reconfigure'):
    sys.stderr.reconfigure(line_buffering=True)

try:
    import serial
    import serial.tools.list_ports
except ImportError:
    print(json.dumps({'success': False, 'error': 'pyserial module not installed. Run: pip install pyserial'}), flush=True)
    sys.exit(1)

def find_esp32_port():
    """Auto-detect ESP32/Wemos serial ports"""
    ports = serial.tools.list_ports.comports()
    candidates = []
    for port in ports:
        desc = port.description.lower()
        if any(x in desc for x in ['ch340', 'usb-serial', 'cp210', 'ftdi', 'wch', 'usb']):
            candidates.append(port.device)
    return candidates or ['COM4', 'COM3', 'COM5', 'COM6', '/dev/ttyUSB0']

def send_command(ser, command, wait_sec=3):
    """Send command to WEMOS and get response"""
    try:
        cmd = f'{command}\n'
        print(f'[DEBUG] Sending: {cmd.strip()}', file=sys.stderr, flush=True)
        ser.write(cmd.encode())
        ser.flush()
        
        response_lines = []
        start_time = time.time()
        no_data_count = 0
        data_received = False
        
        while time.time() - start_time < wait_sec:
            if ser.in_waiting:
                try:
                    line = ser.readline().decode('utf-8', errors='ignore').strip()
                    if line:
                        print(f'[DEBUG] Got: {line}', file=sys.stderr, flush=True)
                        response_lines.append(line)
                        no_data_count = 0
                        data_received = True
                    else:
                        break
                except Exception as e:
                    print(f'[DEBUG] Read error: {e}', file=sys.stderr, flush=True)
            else:
                no_data_count += 1
                if data_received and no_data_count > 30:  # Stop waiting if we got data and no more coming
                    print(f'[DEBUG] No more data incoming', file=sys.stderr, flush=True)
                    break
            time.sleep(0.05)
        
        response = '\n'.join(response_lines)
        print(f'[DEBUG] Final response: {response if response else "(empty)"}', file=sys.stderr, flush=True)
        
        # Consider empty response as success too (some commands might not return anything)
        return {
            'success': True,
            'command': command,
            'response': response
        }
    except Exception as e:
        error_msg = f'{str(e)} - {traceback.format_exc()}'
        print(f'[DEBUG] Exception: {error_msg}', file=sys.stderr, flush=True)
        return {'success': False, 'error': error_msg}

def trigger_servo(port, duration=5):
    """Trigger servo to open for N seconds"""
    max_retries = 3
    retry_delay = 1  # seconds
    
    for attempt in range(max_retries):
        try:
            print(f'[DEBUG] Opening port {port} at 115200 baud (attempt {attempt+1}/{max_retries})', file=sys.stderr, flush=True)
            
            # Try to close any existing connection first
            try:
                ser_test = serial.Serial(port)
                ser_test.close()
                time.sleep(0.5)
            except:
                pass
            
            # Now open fresh
            ser = serial.Serial(port, 115200, timeout=2)
            time.sleep(2.5)  # Wait for board to stabilize
            
            # Clear any existing data in buffer
            ser.reset_input_buffer()
            ser.reset_output_buffer()
            
            result = send_command(ser, f'TRIGGER {duration}', wait_sec=3)  # Reduced timeout
            ser.close()
            time.sleep(0.5)  # Brief pause after close
            
            return result
            
        except PermissionError as e:
            error_msg = f'Port {port} is locked/in-use. Close Arduino IDE or other serial tools. Retry {attempt+1}/{max_retries}'
            print(f'[DEBUG] {error_msg}', file=sys.stderr, flush=True)
            
            if attempt < max_retries - 1:
                time.sleep(retry_delay)
            else:
                print(f'[DEBUG] Max retries exceeded', file=sys.stderr, flush=True)
                return {'success': False, 'error': error_msg}
                
        except Exception as e:
            error_msg = f'Port error on {port}: {str(e)} - {traceback.format_exc()}'
            print(f'[DEBUG] {error_msg}', file=sys.stderr, flush=True)
            return {'success': False, 'error': error_msg}
    
    return {'success': False, 'error': f'Failed to open {port} after {max_retries} attempts'}

def read_sensor(port):
    """Read temperature and humidity from DHT22"""
    max_retries = 3
    retry_delay = 1  # seconds
    
    for attempt in range(max_retries):
        try:
            print(f'[DEBUG] Opening port {port} at 115200 baud (attempt {attempt+1}/{max_retries})', file=sys.stderr, flush=True)
            
            # Try to close any existing connection first
            try:
                ser_test = serial.Serial(port)
                ser_test.close()
                time.sleep(0.5)
            except:
                pass
            
            # Now open fresh
            ser = serial.Serial(port, 115200, timeout=2)
            time.sleep(1.5)
            
            # Clear any existing data in buffer
            ser.reset_input_buffer()
            ser.reset_output_buffer()
            
            result = send_command(ser, 'SENSOR', wait_sec=5)
            ser.close()
            time.sleep(0.5)  # Brief pause after close
            
            if result['success'] and result['response']:
                response = result['response']
                data = parse_sensor_response(response)
                result['data'] = data
            
            return result
            
        except PermissionError as e:
            error_msg = f'Port {port} is locked/in-use. Close Arduino IDE or other serial tools. Retry {attempt+1}/{max_retries}'
            print(f'[DEBUG] {error_msg}', file=sys.stderr, flush=True)
            
            if attempt < max_retries - 1:
                time.sleep(retry_delay)
            else:
                print(f'[DEBUG] Max retries exceeded', file=sys.stderr, flush=True)
                return {'success': False, 'error': error_msg}
                
        except Exception as e:
            error_msg = f'Port error on {port}: {str(e)} - {traceback.format_exc()}'
            print(f'[DEBUG] {error_msg}', file=sys.stderr, flush=True)
            return {'success': False, 'error': error_msg}
    
    return {'success': False, 'error': f'Failed to open {port} after {max_retries} attempts'}

def parse_sensor_response(response):
    """Parse sensor response to extract temperature and humidity"""
    data = {'temperature': None, 'humidity': None}
    
    lines = response.split('\n')
    for line in lines:
        if 'Temperature:' in line and '°C' in line:
            try:
                temp = line.split('Temperature:')[1].split('°C')[0].strip()
                data['temperature'] = float(temp)
            except:
                pass
        if 'Humidity:' in line and '%' in line:
            try:
                humidity = line.split('Humidity:')[1].split('%')[0].strip()
                data['humidity'] = float(humidity)
            except:
                pass
    
    return data

def main():
    try:
        if len(sys.argv) < 2:
            output = {'error': 'No action specified', 'usage': 'trigger | sensor | both'}
            print(json.dumps(output), flush=True)
            sys.exit(1)
        
        action = sys.argv[1]
        duration = int(sys.argv[2]) if len(sys.argv) > 2 else 5
        port = sys.argv[3] if len(sys.argv) > 3 else None
        
        print(f'[DEBUG] Action: {action}, Duration: {duration}, Port: {port}', file=sys.stderr, flush=True)
        
        # Auto-detect port if not specified
        if port is None:
            candidates = find_esp32_port()
            print(f'[DEBUG] Trying ports: {candidates}', file=sys.stderr, flush=True)
            
            for p in candidates:
                try:
                    ser = serial.Serial(p, 115200, timeout=0.5)
                    time.sleep(0.5)
                    ser.close()
                    port = p
                    print(f'[DEBUG] Found port: {p}', file=sys.stderr, flush=True)
                    break
                except:
                    pass
        
        if port is None:
            output = {
                'success': False,
                'error': 'Could not find WEMOS board. Check USB connection.',
            }
            print(json.dumps(output), flush=True)
            sys.exit(1)
        
        # Process action
        result = {}
        
        if action == 'trigger':
            print(f'[DEBUG] Triggering servo for {duration}s on {port}', file=sys.stderr, flush=True)
            result = trigger_servo(port, duration)
        
        elif action == 'sensor':
            print(f'[DEBUG] Reading sensors on {port}', file=sys.stderr, flush=True)
            result = read_sensor(port)
        
        elif action == 'both':
            print(f'[DEBUG] Triggering servo and reading sensors on {port}', file=sys.stderr, flush=True)
            servo_result = trigger_servo(port, duration)
            sensor_result = read_sensor(port)
            
            result = {
                'success': servo_result['success'] and sensor_result['success'],
                'servo': servo_result,
                'sensor': sensor_result
            }
        
        else:
            result = {
                'success': False,
                'error': f'Unknown action: {action}',
            }
        
        print(json.dumps(result, indent=2), flush=True)
        print(f'[DEBUG] Exiting with success={result.get("success", False)}', file=sys.stderr, flush=True)
        sys.exit(0 if result.get('success', False) else 1)
        
    except Exception as e:
        error_result = {
            'success': False,
            'error': str(e),
            'traceback': traceback.format_exc()
        }
        print(json.dumps(error_result, indent=2), flush=True)
        print(f'[DEBUG] Fatal error: {traceback.format_exc()}', file=sys.stderr, flush=True)
        sys.exit(1)

if __name__ == '__main__':
    main()

