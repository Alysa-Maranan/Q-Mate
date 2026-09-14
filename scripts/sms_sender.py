#!/usr/bin/env python
import sys
import time
import os
try:
    import serial
except Exception:
    print('Missing pyserial. Install: pip install pyserial')
    sys.exit(2)

def usage():
    print('Usage: sms_sender.py <phone_number> <message> [port]')
    print('Example: sms_sender.py +639123456789 "Feeding started" COM3')

def send_sms(phone_number, message, port=None):
    # Get port from environment variable if not specified
    if not port:
        port = os.getenv('GSM_SERIAL_PORT')

    # Default ports to try
    candidates = [port] if port else []
    candidates += ['COM3', 'COM5', 'COM6', '/dev/ttyUSB1', '/dev/ttyACM1']

    ser = None
    for p in candidates:
        if not p:
            continue
        try:
            # Arduino uses 9600 baud
            ser = serial.Serial(p, 9600, timeout=3)
            print(f'Connected to Arduino on {p}')
            time.sleep(2)  # Wait for Arduino to initialize
            break
        except Exception as e:
            print(f'Failed to connect to {p}: {e}')
            continue

    if ser is None:
        print('Could not open serial port. Set port as 3rd arg or GSM_SERIAL_PORT env var.')
        return False

    try:
        # Send SMS command: SMS|<phone>|<message>
        command = f'SMS|{phone_number}|{message}\n'
        print(f'Sending: {command.strip()}')

        ser.write(command.encode('utf-8'))
        ser.flush()

        # Wait for response
        time.sleep(15)  # SMS takes time to send
        if ser.in_waiting:
            response = ser.read(ser.in_waiting).decode('utf-8', errors='ignore')
            print(f'Arduino Response:\n{response}')

            if 'OK' in response and 'SMS Sent' in response:
                print('✅ SMS sent successfully!')
                return True
            else:
                print('❌ SMS failed to send')
                return False
        else:
            print('⚠️ No response from Arduino')
            return False

    except Exception as e:
        print(f'Error during communication: {e}')
        return False
    finally:
        ser.close()

def main():
    if len(sys.argv) < 3:
        usage()
        sys.exit(1)

    phone_number = sys.argv[1]
    message = sys.argv[2]
    port = sys.argv[3] if len(sys.argv) > 3 else None

    # Validate phone number format
    if not phone_number.startswith('+'):
        print('Warning: Phone number should start with + (e.g., +639123456789)')

    success = send_sms(phone_number, message, port)
    sys.exit(0 if success else 1)

if __name__ == '__main__':
    main()
