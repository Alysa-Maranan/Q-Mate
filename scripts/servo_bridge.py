#!/usr/bin/env python
"""
Servo Bridge - python servo_bridge.py COM3
Background service that watches for servo_command.json and controls the servo.

FAST Synchronization Design (no more lag):
- Serial port is kept OPEN persistently -> zero 2s boot wait on every command
  (the 2s Wemos boot wait only happens once at startup / on reconnect)
- servo_command.json is checked every 100ms -> command noticed almost instantly
- OPEN is sent immediately, and animation_trigger.json is written at the EXACT
  MOMENT the servo opens -> browser animation starts with the servo (~300ms)
- CLOSE is sent immediately after duration, and animation_trigger.json is
  deleted at the EXACT MOMENT the servo closes -> animation hides instantly
- ACK waits reduced 1s -> 0.15s (they were only for logging anyway)
- Works for ALL feed types (manual + scheduled)
"""
import sys
import time
import os
import json
import serial
import serial.tools.list_ports

BASE        = os.path.join(os.path.dirname(__file__), '..', 'storage', 'logs')
CMD_FILE    = os.path.join(BASE, 'servo_command.json')
RESULT_FILE = os.path.join(BASE, 'servo_result.json')
LOCK_FILE   = os.path.join(BASE, 'serial.lock')
ANIM_FILE   = os.path.join(BASE, 'animation_trigger.json')
BAUD        = 115200  # Must match the Servo.ino firmware

BOOT_WAIT = 2.0    # ONLY on first connect / reconnect (Wemos boot after DTR reset)
ACK_WAIT  = 0.15   # was 1.0s - just long enough to catch the Wemos reply for logging
LOOP_WAIT = 0.1    # was 1.0s - how fast the bridge notices servo_command.json

def ts():
    return time.strftime('%H:%M:%S')

def get_connection(state, port):
    """Return an already-open persistent serial connection.

    The connection is reused between commands so each command is INSTANT.
    The 2s Wemos boot wait only happens on the very first connect (or when
    the device was unplugged and we need to reconnect)."""
    ser = state.get('ser')
    if ser is not None and ser.is_open and state.get('port') == port:
        return ser

    if ser is not None and ser.is_open:
        try:
            ser.close()
        except Exception:
            pass

    print(f'[{ts()}] Opening serial port {port} at {BAUD} baud...', flush=True)
    # Open port WITHOUT resetting DTR/RTS to avoid Wemos reboot
    ser = serial.Serial()
    ser.port = port
    ser.baudrate = BAUD
    ser.timeout = 5
    ser.dtr = False
    ser.rts = False
    ser.open()
    time.sleep(BOOT_WAIT)   # needed only ONCE - Wemos boots when port first opens
    ser.reset_input_buffer()

    state['ser'] = ser
    state['port'] = port
    print(f'[{ts()}] Serial connection ready - next commands are INSTANT', flush=True)
    return ser

def drop_connection(state):
    ser = state.get('ser')
    if ser is not None:
        try:
            if ser.is_open:
                ser.close()
        except Exception:
            pass
    state['ser'] = None
    state['port'] = None

def send(state, port, payload):
    """Write a command to the Wemos. On failure, reconnect once and retry."""
    try:
        ser = get_connection(state, port)
        ser.write(payload)
        ser.flush()
        return ser
    except Exception as e:
        print(f'[{ts()}] Serial write failed ({e}) - reconnecting...', flush=True)
        drop_connection(state)
        ser = get_connection(state, port)   # includes BOOT_WAIT (recovery only)
        ser.write(payload)
        ser.flush()
        return ser

def read_ack(ser, wait=ACK_WAIT):
    """Log any Wemos reply. Never blocks the animation sync (kept very short)."""
    time.sleep(wait)
    if ser is not None and ser.in_waiting:
        data = ser.read(ser.in_waiting).decode('utf-8', errors='ignore').strip()
        if data:
            print(f'[{ts()}] Wemos: {data}', flush=True)

def configured_port():
    env_port = os.getenv('SERVO_SERIAL_PORT')
    if env_port:
        return env_port

    laravel_env = os.path.join(os.path.dirname(__file__), '..', '.env')
    if os.path.exists(laravel_env):
        with open(laravel_env, 'r', encoding='utf-8') as env_file:
            for line in env_file:
                line = line.strip()
                if line.startswith('SERVO_SERIAL_PORT='):
                    return line.split('=', 1)[1].strip().strip('"\'')
    return None

def find_port():
    for p in serial.tools.list_ports.comports():
        if any(x in p.description.lower() for x in ['ch340', 'cp210', 'ftdi', 'wch', 'usb-serial']):
            return p.device
    return configured_port() or 'COM3'

def do_trigger(state, port, duration, cage=1, feed_type='manual'):
    open(LOCK_FILE, 'w').close()
    try:
        # Serial connection is persistent -> this is INSTANT (no 2s boot wait)
        print(f'[{ts()}] Sending OPEN command...', flush=True)
        ser = send(state, port, b'OPEN\n')

        # Create animation_trigger.json for ALL feed types (manual + scheduled)
        # Synchronized: written AT THE EXACT MOMENT the servo opens, so the
        # browser shows the animation at the same instant the servo opens.
        with open(ANIM_FILE, 'w') as f:
            json.dump({
                'active': True,
                'duration': duration,
                'cage': cage,
                'started_at': time.strftime('%Y-%m-%dT%H:%M:%S+08:00'),
                'type': feed_type
            }, f)
        print(f'[{ts()}] SERVO OPENED + animation active ({feed_type})', flush=True)

        read_ack(ser)   # log-only, 0.15s

        print(f'[{ts()}] Feeding for {duration} seconds...', flush=True)
        time.sleep(duration)

        print(f'[{ts()}] Sending CLOSE command...', flush=True)
        ser = send(state, port, b'CLOSE\n')

        # Delete animation_trigger.json AT THE EXACT MOMENT the servo closes,
        # so the browser hides the animation at the same instant the servo closes.
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        print(f'[{ts()}] SERVO CLOSED - animation stopped - done!', flush=True)

        read_ack(ser)   # log-only, 0.15s

        # NOTE: keep the serial connection OPEN (persistent) - do NOT close it,
        # closing would force another 2s boot wait on the next command.
        return {'success': True, 'duration': duration}

    except Exception as e:
        print(f'[{ts()}] ERROR: {e}', flush=True)
        drop_connection(state)   # force clean reconnect on next command
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        return {'success': False, 'error': str(e)}
    finally:
        if os.path.exists(LOCK_FILE):
            os.remove(LOCK_FILE)

def do_close(state, port):
    """Handle an immediate close command (no prior open). Used by closeServo()."""
    open(LOCK_FILE, 'w').close()
    try:
        # Serial connection is persistent -> this is INSTANT (no 2s boot wait)
        print(f'[{ts()}] Sending CLOSE...', flush=True)
        ser = send(state, port, b'CLOSE\n')

        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        print(f'[{ts()}] SERVO CLOSED - animation stopped - done!', flush=True)

        read_ack(ser)   # log-only, 0.15s

        # Keep the connection open (persistent) for the next command
        return {'success': True}
    except Exception as e:
        print(f'[{ts()}] ERROR: {e}', flush=True)
        drop_connection(state)
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        return {'success': False, 'error': str(e)}
    finally:
        if os.path.exists(LOCK_FILE):
            os.remove(LOCK_FILE)

def main():
    port = sys.argv[1] if len(sys.argv) > 1 else find_port()
    print(f'[{time.strftime("%H:%M:%S")}] ========================================', flush=True)
    print(f'[{time.strftime("%H:%M:%S")}] Servo Bridge Started on {port}', flush=True)
    print(f'[{time.strftime("%H:%M:%S")}] Watching: {CMD_FILE}', flush=True)
    print(f'[{time.strftime("%H:%M:%S")}] Waiting for commands...', flush=True)
    print(f'[{time.strftime("%H:%M:%S")}] ========================================', flush=True)

    state = {'ser': None, 'port': None}   # persistent serial connection

    while True:
        try:
            if os.path.exists(CMD_FILE):
                with open(CMD_FILE, 'r') as f:
                    cmd = json.load(f)
                os.remove(CMD_FILE)

                action    = cmd.get('action', 'trigger')
                duration  = int(cmd.get('duration', 5))
                cage      = int(cmd.get('cage', 1))
                feed_type = cmd.get('type', 'manual')
                cmd_port  = cmd.get('port', port)

                if action == 'close':
                    # Immediate close command (from closeServo() PHP helper)
                    print(f'[{ts()}] CLOSE command received', flush=True)
                    result = do_close(state, cmd_port)
                else:
                    print(f'[{ts()}] Command received: {duration}s cage {cage} ({feed_type})', flush=True)
                    result = do_trigger(state, cmd_port, duration, cage, feed_type)

                with open(RESULT_FILE, 'w') as f:
                    json.dump({**result, 'port': port, 'timestamp': time.time()}, f)

                if result['success']:
                    print(f'[{ts()}] SUCCESS', flush=True)
                else:
                    print(f'[{ts()}] FAILED: {result.get("error")}', flush=True)

        except Exception as e:
            print(f'[{ts()}] Bridge error: {e}', flush=True)

        time.sleep(LOOP_WAIT)   # 100ms - notice new commands almost instantly

if __name__ == '__main__':
    main()
