#!/usr/bin/env python
"""
DEEP Servo Diagnostic for WEMOS D1 R1 (SQUIFM feeder)
May mahabang wait windows (12s bawat command) dahil mabagal tumugon ang
firmware kapag abala sa WiFi HTTP calls. Sinasabi din nito ang "paniniwala"
ng firmware sa posisyon ng servo - para malaman kung software o hardware.

Uso:  python scripts\\test_servo_deep.py [COM_PORT]

PANOORIN ANG SERVO HABANG TUMATAKBO ANG TEST!
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
    print(f'=== DEEP SERVO TEST on {port} @ {BAUD} baud ===')
    print('>>> PANOORIN ANG SERVO MOTOR NGAYON <<<')

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
        print('  -> May ibang gumagamit ng port? taskkill /IM python.exe /F')
        sys.exit(1)

    print(f'[OK] {port} opened')
    time.sleep(2)
    if ser.in_waiting:
        boot = ser.read(ser.in_waiting).decode('utf-8', errors='ignore').strip()
        if boot:
            print('--- firmware output ---')
            print(boot[:800])
            print('-----------------------')

    def send(cmd, wait=12):
        print(f'\n>>> SEND: {cmd}  (hinihintay ang sagot hanggang {wait}s...)')
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
            print('    << (WALANG SAGOT sa loob ng wait window)')
        return '\n'.join(lines)

    # 1) Initial state
    send('STATUS', 12)

    # 2) OPEN + verify firmware's BELIEF about the position
    open_resp = send('OPEN', 12)
    print('    *** DAPAT GUMAGALAW ANG SERVO NGAYON (pa-open 180 deg) ***')
    time.sleep(2)
    status_open = send('STATUS', 12)

    # 3) CLOSE + verify
    close_resp = send('CLOSE', 12)
    time.sleep(2)
    status_closed = send('STATUS', 12)

    ser.close()

    print('\n=== RESULTA NG DIAGNOSIS ===')
    firmware_thinks_open = 'Servo Status: OPEN' in status_open or 'Position: 180' in status_open
    firmware_thinks_closed = 'Servo Status: CLOSED' in status_closed or 'Position: 0' in status_closed
    got_open_ack = 'SERVO: OPEN' in open_resp or 'POSITION: 180' in open_resp
    got_close_ack = 'SERVO: CLOSED' in close_resp or 'POSITION: 0' in close_resp

    print(f'Firmware OPEN ack        : {"OO" if got_open_ack else "HINDI"}')
    print(f'Firmware STATUS habang open: {"OPEN/180 (naintindihan ng firmware)" if firmware_thinks_open else "hindi na-confirm"}')
    print(f'Firmware CLOSE ack       : {"OO" if got_close_ack else "HINDI"}')
    print(f'Firmware STATUS pagkatapos: {"CLOSED/0 (balik na)" if firmware_thinks_closed else "hindi na-confirm"}')
    print()
    if firmware_thinks_open and not got_open_ack:
        print('>> Firmware naka-OPEN pero walang agad na ack = WiFi-blocking delay lang. OK ang serial.')
    if firmware_thinks_open:
        print('>> Kung HINDI gumalaw ang servo kahit "OPEN/180" ang firmware:')
        print('   1. Tignan kung saang PIN nakasaksak ang ORANGE/SIGNAL wire ng servo')
        print('      - Dapat ay D5 (tingnan ang HARDWARE_WIRING_GUIDE.md)')
        print('   2. Servo power: dapat EXTERNAL 5V (hindi lang galing sa WEMOS 5V pin)')
        print('   3. COMMON GROUND: GND ng servo supply = GND ng WEMOS')
        print('   4. Kung tama lahat = posibleng sira ang servo mismo')


if __name__ == '__main__':
    main()
