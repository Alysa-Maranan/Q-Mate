#!/usr/bin/env python
"""
Servo control via bridge file — works even when called by Apache/SYSTEM.
Writes a command file that servo_bridge.py picks up and executes.
"""
import sys
import time
import os
import json

BASE        = os.path.join(os.path.dirname(__file__), '..', 'storage', 'logs')
CMD_FILE    = os.path.join(BASE, 'servo_command.json')
RESULT_FILE = os.path.join(BASE, 'servo_result.json')

def main():
    if len(sys.argv) < 2 or sys.argv[1] != 'trigger':
        print('Usage: servo_control.py trigger [duration] [port]')
        sys.exit(1)

    duration = int(sys.argv[2]) if len(sys.argv) > 2 else 5
    port     = sys.argv[3] if len(sys.argv) > 3 else os.getenv('SERVO_SERIAL_PORT', 'COM3')

    # Remove stale result file
    if os.path.exists(RESULT_FILE):
        os.remove(RESULT_FILE)

    # Replace the command atomically so the bridge never reads partial JSON.
    temporary_command_file = CMD_FILE + f'.tmp.{os.getpid()}'
    with open(temporary_command_file, 'w', encoding='utf-8') as f:
        json.dump({'action': 'trigger', 'duration': duration, 'port': port}, f)
    os.replace(temporary_command_file, CMD_FILE)

    print(f'Command written. Waiting for bridge to execute ({duration}s)...')

    # Wait for result (duration + 10s timeout)
    timeout = duration + 10
    start   = time.time()
    while time.time() - start < timeout:
        if os.path.exists(RESULT_FILE):
            with open(RESULT_FILE, 'r') as f:
                result = json.load(f)
            if result.get('success'):
                print('OK')
                sys.exit(0)
            else:
                print(f'FAIL: {result.get("error")}')
                sys.exit(1)
        time.sleep(0.5)

    print('TIMEOUT: servo_bridge.py may not be running. Start it with: start_servo_bridge.bat')
    sys.exit(2)

if __name__ == '__main__':
    main()
