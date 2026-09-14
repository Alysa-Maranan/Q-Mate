#!/usr/bin/env python
"""
CLEAN Servo Probe - paganahin ito pagkatapos i-RESET ang WEMOS
(pindutin ang EN/RST button o i-unplug-replug ang USB cable)

Isa-isa lang ang command: OPEN -> STATUS -> CLOSE, mahabang wait window.
WALANG ibang programang gumagamit ng COM port habang tumatakbo ito.

Uso:  python scripts\\test_servo_clean.py [COM_PORT]
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
    print(f'=== CLEAN SERVO PROBE on {port} @ {BAUD} baud ===')
    print('>>> PANOORIN ANG SERVO MOTOR <<<')

    try:
        ser = serial.Serial()
        ser.port = port
        ser.baudrate = BAUD
        ser.timeout = 2
        ser.dtr = False
        ser.rts = False
        ser.open()
    except Exception as e:
        print(f'[FAIL] Could not open {port}: {e}')
        sys.exit(1)

    print(f'[OK] {port} opened. Hintayin ang reboot output ng WEMOS (hanggang 20s)...')

    # Kunin ang boot banner - patunay na bagong-reboot ang WEMOS
    end_time = time.time() + 20
    boot_lines = []
    while time.time() < end_time:
        if ser.in_waiting:
            line = ser.readline().decode('utf-8', errors='ignore').strip()
            if line:
                boot_lines.append(line)
                print(f'    << {line}')
                if 'SYSTEM STARTED' in line:
                    break
        else:
            time.sleep(0.05)
    if any('SYSTEM STARTED' in l for l in boot_lines):
        print('[OK] NAKITA ANG BOOT BANNER - sariwang reboot ang WEMOS, malinis ang RX buffer!')
    else:
        print('[WARN] Walang boot banner - baka hindi na-reset ang WEMOS. Pindutin ang EN/RST.')

    def send(cmd, wait=25):
        print(f'\n>>> SEND: {cmd} (wait hanggang {wait}s)')
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
            print('    << (WALANG SAGOT)')
        return '\n'.join(lines)

    open_resp = send('OPEN', 25)
    time.sleep(2)
    status_open = send('STATUS', 25)
    close_resp = send('CLOSE', 25)

    ser.close()

    print('\n=== KONKLUSYON ===')
    if 'SERVO: OPEN' in open_resp or 'POSITION: 180' in open_resp:
        print('[FIRMWARE OK] Kinumpirma ng WEMOS ang OPEN command!')
        if 'Servo Status: OPEN' in status_open or 'Position: 180' in status_open:
            print('[FIRMWARE OK] STATUS = OPEN/180. Ang firmware ALAM na bukas ang servo.')
            print('>> Kung HINDI gumalaw ang servo nang physical:')
            print('   1. Signal wire (orange) dapat naka-D5')
            print('   2. Servo power: EXTERNAL 5V supply (hindi galing WEMOS)')
            print('   3. Common ground: GND ng supply = GND ng WEMOS')
            print('   4. Kung tama lahat - baka sira ang servo, try ng ibang servo')
        else:
            print('[?] May ack pero hindi na-confirm ng STATUS - paki-report ang output sa itaas')
    elif 'ERROR: Unknown command' in open_resp:
        print('[FIRMWARE MISMATCH] Iba/old ang naka-flash na sketch - kailangan i-upload ulit ang hardware.ino!')
    else:
        print('[PROBLEM HINDI PA RIN] Walang ack kahit mag-reset na -')
        print('   -> kailangan nating i-flash muli ang firmware (serial-only version)')


if __name__ == '__main__':
    main()
