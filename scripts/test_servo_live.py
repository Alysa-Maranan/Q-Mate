#!/usr/bin/env python
"""
Live Servo Diagnostic for WEMOS D1 R1 (SQUIFM feeder)
Sends STATUS, OPEN, CLOSE directly over serial and prints EVERY response
the firmware gives back. Use this to prove whether the problem is
software (bridge) or hardware (wiring/power/servo).

Usage:  python scripts\\test_servo_live.py [COM_PORT]
"""
import sys
import time
import os
import serial

BAUD = 115200


def configured_port():
    laravel_env = os.path.join(os.path.dirname(__file__), '..', '.env')
    if os.path.exists(laravel_env):
        with open(laravel_env, 'r', encoding='utf-8') as env_file:
            for line in env_file:
                line = line.strip()
                if line.startswith('SERVO_SERIAL_PORT='):
                    return line.split('=', 1)[1].strip().strip('"\'')
    return 'COM11'


def main():
    port = sys.argv[1] if len(sys.argv) > 1 else configured_port()
    print(f'=== LIVE SERVO TEST on {port} @ {BAUD} baud ===')

    try:
        ser = serial.Serial()
        ser.port = port
        ser.baudrate = BAUD
        ser.timeout = 2
        ser.dtr = False   # avoid resetting the WEMOS
        ser.rts = False
        ser.open()
    except Exception as e:
        print(f'[FAIL] Could not open {port}: {e}')
        print('  -> Another program is using the port? Kill python/pythonw first:')
        print('     taskkill /IM python.exe /F & taskkill /IM pythonw.exe /F')
        sys.exit(1)

    print(f'[OK] {port} opened')
    time.sleep(2)  # let the WEMOS settle / flush boot output
    if ser.in_waiting:
        boot = ser.read(ser.in_waiting).decode('utf-8', errors='ignore').strip()
        if boot:
            print('--- firmware boot output ---')
            print(boot)
            print('----------------------------')

    def send(cmd, wait=4):
        print(f'\n>>> SEND: {cmd}')
        ser.reset_input_buffer()
        ser.write(f'{cmd}\n'.encode())
        ser.flush()
        end_time = time.time() + wait
        lines = []
        while time.time() < end_time:
            if ser.in_waiting:
                line = ser.readline().decode('utf-8', errors='ignore').strip()
                if line:
                    lines.append(line)
                    print(f'    << {line}')
            else:
                time.sleep(0.05)
        if not lines:
            print('    << (NO RESPONSE!)')
        return '\n'.join(lines)

    status = send('STATUS', 5)
    open_resp = send('OPEN', 4)
    if 'POSITION: 180' in open_resp:
        print('    [FIRMWARE OK] Servo commanded to 180 degrees.')
        print('    >>> WATCH THE SERVO NOW - it must physically move! <<<')
    elif open_resp:
        print('    [WARN] Firmware answered but NOT with a valid OPEN ack.')
    time.sleep(3)
    close_resp = send('CLOSE', 4)
    if 'POSITION: 0' in close_resp:
        print('    [FIRMWARE OK] Servo commanded back to 0 degrees.')

    ser.close()
    print('\n=== TEST DONE - INTERPRETATION ===')
    print('1) Saw "SERVO: OPEN / POSITION: 180" but servo did NOT move')
    print('   -> HARDWARE issue: wiring (D5 signal), power, or dead servo')
    print('2) Saw "ERROR: Unknown command"')
    print('   -> Different/old sketch flashed on the WEMOS - reflash hardware.ino')
    print('3) NO response at all')
    print('   -> Firmware busy/blocked (WiFi) or reset-looping - check USB cable/port')


if __name__ == '__main__':
    main()
