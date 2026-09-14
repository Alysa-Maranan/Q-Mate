#!/usr/bin/env python
"""
Unified Hardware Bridge for WEMOS D1 R1
Handles: Servo Motor (MG996R), DHT22, HC-SR04 Ultrasonic Sensor
Single script to avoid COM port conflicts

Usage: python unified_bridge.py [COM_PORT]
"""
import sys
import time
import os
import json
import re
import serial
import serial.tools.list_ports
import urllib.request
from json import JSONDecodeError

# ============== CONFIGURATION ==============
BASE        = os.path.join(os.path.dirname(__file__), '..', 'storage', 'logs')
CMD_FILE    = os.path.join(BASE, 'servo_command.json')
RESULT_FILE = os.path.join(BASE, 'servo_result.json')
ANIM_FILE   = os.path.join(BASE, 'animation_trigger.json')
def configured_value(name, default):
    value = os.getenv(name)
    if value:
        return value

    laravel_env = os.path.join(os.path.dirname(__file__), '..', '.env')
    if os.path.exists(laravel_env):
        with open(laravel_env, 'r', encoding='utf-8') as env_file:
            for line in env_file:
                line = line.strip()
                if line.startswith(f'{name}='):
                    return line.split('=', 1)[1].strip().strip('"\'')
    return default

API_BASE_URL = configured_value(
    "HARDWARE_API_BASE_URL",
    "http://127.0.0.1:8000"
).rstrip("/")
API_URL     = f"{API_BASE_URL}/api/sensor-data"
BAUD        = 115200
SENSOR_INTERVAL = 60  # Read and publish DHT22 data once per minute
FOOD_LEVEL_INTERVAL = 15  # Dati 2s - binabara nito ang RX buffer ng WEMOS!

# ============== SINGLE INSTANCE LOCK ==============
# Prevents two bridges from fighting over the COM port
# ("could not open port: Access is denied" race). Windows releases the
# mutex automatically when the process dies, so no stale locks are possible.
import ctypes
import msvcrt
import subprocess

_MUTEX_NAME = "Global\\QuailUnifiedBridgeMutex"
_mutex_handle = None
_INSTANCE_FILE = os.path.join(BASE, 'unified_bridge.instance')
_instance_file_handle = None

def _pid_alive(pid):
    """Windows-safe liveness check. HINDI nito pinapatay ang process.
    (Ang os.kill(pid, 0) sa Windows ay Hindi simpleng check - TerminateProcess
    talaga ang ginagawa nito kapag buhay ang PID, at nag-e-raise ng OSError
    kapag patay na - kaya nasira ang stale-lock recovery noon.)"""
    try:
        output = subprocess.run(
            ['tasklist', '/FI', f'PID eq {pid}', '/NH'],
            capture_output=True, text=True, timeout=5,
            creationflags=subprocess.CREATE_NO_WINDOW,
        ).stdout
        return str(pid) in output
    except Exception:
        return True  # Hindi ma-verify? Safe assumption: buhay pa.


def acquire_instance_lock():
    """Returns True if this is the ONLY bridge instance allowed to run."""
    global _mutex_handle, _instance_file_handle
    try:
        _instance_file_handle = os.open(_INSTANCE_FILE, os.O_CREAT | os.O_EXCL | os.O_RDWR)
        msvcrt.locking(_instance_file_handle, msvcrt.LK_NBLCK, 1)
        os.write(_instance_file_handle, str(os.getpid()).encode('ascii'))
        return True
    except FileExistsError:
        # A crashed / force-killed (taskkill /F) bridge leaves its marker file
        # behind. Ito ay STALE LANG kung patay na ang PID na nakasulat dito.
        previous_pid = None
        try:
            with open(_INSTANCE_FILE, 'r', encoding='ascii') as lock_file:
                previous_pid = int(lock_file.read().strip())
        except (FileNotFoundError, ValueError, OSError):
            previous_pid = None

        if previous_pid is not None and _pid_alive(previous_pid):
            print(f'[{time.strftime("%H:%M:%S")}] Another Unified Bridge is already running — exiting.', flush=True)
            return False

        # Walang buhay na bridge — stale marker lang, burahin at i-retry.
        try:
            os.remove(_INSTANCE_FILE)
        except (FileNotFoundError, PermissionError, OSError):
            print(f'[{time.strftime("%H:%M:%S")}] Another Unified Bridge is already running — exiting.', flush=True)
            return False
        return acquire_instance_lock()
    except Exception as e:
        if _instance_file_handle is not None:
            os.close(_instance_file_handle)
            _instance_file_handle = None
        print(f'[{time.strftime("%H:%M:%S")}] Bridge lock error: {e}', flush=True)
        return False

def release_instance_lock():
    global _instance_file_handle
    if _instance_file_handle is not None:
        os.close(_instance_file_handle)
        _instance_file_handle = None
    try:
        os.remove(_INSTANCE_FILE)
    except FileNotFoundError:
        pass

# ============== PORT DETECTION ==============
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
        desc = p.description.lower()
        if any(x in desc for x in ['ch340', 'cp210', 'ftdi', 'wch', 'usb-serial']):
            return p.device
    return configured_port()

# ============== PERSISTENT SERIAL CONNECTION ==============
# Nakabukas ang COM port MAGHAPON - isang beses lang ang 2s boot wait.
# Dati: bawat command at bawat sensor read ay muling nagbubukas ng port ->
# nagre-reboot ang WEMOS (DTR reset) + 2 seconds na paghihintay EVERY TIME
# = SOBRANG TAGAL ng pagbukas ng servo at ng animation.
_CONN = {'ser': None, 'port': None}

def ts():
    return time.strftime('%H:%M:%S')

def get_connection(port):
    """Balikan ang nakabukas nang serial connection (o kumonekta kung wala pa)."""
    ser = _CONN['ser']
    if ser is not None and ser.is_open and _CONN['port'] == port:
        return ser
    if ser is not None and ser.is_open:
        try:
            ser.close()
        except Exception:
            pass
    print(f'[{ts()}] Opening serial port {port} at {BAUD} baud...', flush=True)
    ser = open_serial(port)   # may 2s boot wait - FIRST connect / reconnect LANG
    _CONN['ser'] = ser
    _CONN['port'] = port
    print(f'[{ts()}] Serial connection ready - next commands are INSTANT', flush=True)
    return ser

def drop_connection():
    ser = _CONN['ser']
    if ser is not None:
        try:
            if ser.is_open:
                ser.close()
        except Exception:
            pass
    _CONN['ser'] = None
    _CONN['port'] = None

def _send_cmd(port, command, wait_time=3, response_match=None):
    """send_command() gamit ang persistent connection + auto-reconnect + retry."""
    try:
        ser = get_connection(port)
        return send_command(ser, command, wait_time=wait_time, response_match=response_match)
    except Exception as e:
        print(f'[{ts()}] Serial error sa {command} ({e}) - reconnect + retry...', flush=True)
        drop_connection()
        ser = get_connection(port)   # may boot wait - recovery lang ito
        return send_command(ser, command, wait_time=wait_time, response_match=response_match)

# ============== SERIAL COMMUNICATION ==============
def open_serial(port):
    ser = serial.Serial()
    ser.port = port
    ser.baudrate = BAUD
    ser.timeout = 5
    ser.dtr = False
    ser.rts = False
    ser.open()
    time.sleep(2)
    ser.reset_input_buffer()
    return ser

def send_command(ser, command, wait_time=3, response_match=None):
    # Siguraduhing tapos/tapos ang anumang naka-hang na partial line sa
    # RX buffer ng WEMOS bago ang tunay na command (hindi binabara ito
    # kahit malinis na ang buffer - binabale-wala lang ng firmware ang
    # empty line, tingnan ang `command.length() > 0` check sa sketch).
    ser.write(b'\n')
    ser.flush()
    time.sleep(0.05)
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
                    response = '\n'.join(response_lines)
                    if response_match and response_match(response):
                        break
            except serial.SerialException:
                raise
        else:
            if response_lines and response_match is None:
                break
            time.sleep(0.1)
    return '\n'.join(response_lines)


# ============== SENSOR READING ==============
def parse_sensor(response):
    temp = hum = None
    m = re.search(r'Temperature:\s*([\d.]+)', response)
    if m:
        temp = float(m.group(1))
    m = re.search(r'Humidity:\s*([\d.]+)', response)
    if m:
        hum = float(m.group(1))
    return temp, hum

def post_to_api(temperature, humidity):
    payload = json.dumps({"temperature": temperature, "humidity": humidity}).encode()
    req = urllib.request.Request(
        API_URL, data=payload,
        headers={"Content-Type": "application/json"}, method="POST"
    )
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
            return json.loads(resp.read().decode())
    except Exception as e:
        return {"success": False, "error": str(e)}

def read_sensors(port):
    response = ""
    try:
        for attempt in range(1, 4):
            # Persistent connection - WALANG port-open + 2s boot wait dito
            response = _send_cmd(
                port,
                "TEMP",
                wait_time=8,
                response_match=lambda value: (
                    re.search(r'Temperature:\s*[-\d.]+', value) is not None
                    and re.search(r'Humidity:\s*[-\d.]+', value) is not None
                ) or "DHT22 ERROR" in value
            )
            temp, hum = parse_sensor(response)
            if temp is not None and hum is not None:
                return temp, hum, None
            if attempt < 3:
                time.sleep(1)
        print(f'[{ts()}] DHT22: no valid TEMP response after 3 attempts: {response!r}', flush=True)
        return None, None, None
    except Exception as e:
        print(f'[{ts()}] DHT22 read error: {e}', flush=True)
        return None, None, None

def read_food_level(port):
    """Bumasa ng FOOD LEVEL sa PERCENTAGE (%) direkta mula sa firmware.
    Ang IDE/firmware na mismo ang nagco-compute - HINDI na cm."""
    response = ""
    try:
        for attempt in range(1, 4):
            # Persistent connection - WALANG port-open + 2s boot wait dito
            response = _send_cmd(
                port,
                "DISTANCE",
                wait_time=8,
                response_match=lambda value: re.search(
                    r'FEED LEVEL:\s*\d+\s*%', value, re.IGNORECASE
                ) is not None
            )
            m = re.search(r'FEED LEVEL:\s*(\d+)\s*%', response, re.IGNORECASE)
            if m:
                return int(m.group(1))
            if attempt < 3:
                time.sleep(1)
        print(f'[{ts()}] Ultrasonic: no valid DISTANCE response after 3 attempts: {response!r}', flush=True)
        return None
    except Exception as e:
        print(f'[{ts()}] Ultrasonic read error: {e}', flush=True)
        return None


# ============== FOOD LEVEL POST ==============
API_FOOD_URL = f"{API_BASE_URL}/api/food-level"

def post_food_level(level):
    # Diretso PERCENTAGE (%) ang ipinapadala - ang sensor ang magde-decide
    if level is None:
        return None
    payload = json.dumps({"level": level, "source": "ultrasonic"}).encode()
    req = urllib.request.Request(
        API_FOOD_URL, data=payload,
        headers={"Content-Type": "application/json"}, method="POST"
    )
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
            return json.loads(resp.read().decode())
    except Exception as e:
        return {"success": False, "error": str(e)}


# ============== SERVO CONTROL ==============
def open_serial_with_retry(port, attempts=3, delay=1.5):
    """Open the COM port, retrying a few times. Avoids instant failure when
    Windows briefly holds the port after a close (Access denied race)."""
    last_err = None
    for attempt in range(attempts):
        try:
            return open_serial(port)
        except Exception as e:
            last_err = e
            print(f'[{time.strftime("%H:%M:%S")}] Serial open attempt {attempt + 1} failed: {e}', flush=True)
            time.sleep(delay)
    raise last_err

def read_pending_command():
    if not os.path.exists(CMD_FILE):
        return None

    try:
        with open(CMD_FILE, 'r', encoding='utf-8') as command_file:
            raw_command = command_file.read().strip()
        if not raw_command:
            return None
        command = json.loads(raw_command)
        os.remove(CMD_FILE)
        return command
    except JSONDecodeError:
        # The producer may still be replacing the file. Retry on the next loop.
        return None
    except FileNotFoundError:
        return None


def do_trigger(port, duration, cage=1, feed_type='manual'):
    try:
        ser = get_connection(port)

        # TOTOO NA ANG SERVO WINDOW: hinihintay muna ang ack ng firmware sa
        # OPEN BAGO magsimula ang duration timer. Dati, 8s lang ang hintay
        # tapos tuloy agad ang timer - habang ang mabagal na loop ng WEMOS
        # (bara sa WiFi) ay nagpoproseso ng command 30-60s LATE, kaya
        # magkasunod na nag-execute ang OPEN at CLOSE = mili-segundo lang
        # ang totoong bukas ng servo! Sa persistent connection, INSTANT na
        # ang acks; 30s ceiling lang (worst-case WiFi reconnect block).
        _ack = lambda r: ('POSITION:' in r) or ('Unknown command' in r)
        open_resp = _send_cmd(port, "OPEN", wait_time=30, response_match=_ack)
        print(f'[{ts()}] WEMOS replied to OPEN: {open_resp!r}', flush=True)

        # Isusulat ang animation_trigger.json SA SANDALING kumpirmadong
        # bumukas ang servo - synchronized ang browser animation sa TOTOO
        # nitong window (hindi sa oras ng pagpapadala ng command).
        with open(ANIM_FILE, 'w') as f:
            json.dump({
                'active': True,
                'duration': duration,
                'cage': cage,
                'started_at': time.strftime('%Y-%m-%dT%H:%M:%S+08:00'),
                'type': feed_type
            }, f)

        if 'POSITION: 180' in open_resp:
            print(f'[{time.strftime("%H:%M:%S")}] SERVO OPEN CONFIRMED - {duration}s window starting NOW', flush=True)
        else:
            print(f'[{time.strftime("%H:%M:%S")}] WARNING: walang OPEN ack (blind mode) - itutuloy pa rin', flush=True)
        time.sleep(duration)
        close_resp = _send_cmd(port, "CLOSE", wait_time=30, response_match=_ack)
        print(f'[{ts()}] WEMOS replied to CLOSE: {close_resp!r}', flush=True)
        # NOTE: HUWAG i-close ang port - persistent connection para INSTANT
        # ang susunod na command (walang 2s Wemos reboot wait ulit).

        # Delete animation_trigger.json for ALL feed types
        # Synchronized: deleted AT THE EXACT MOMENT the servo closes, so the
        # browser hides the animation at the same instant the servo closes.
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        confirmed = ('POSITION: 180' in open_resp) and ('POSITION: 0' in close_resp)
        return {
            'success': True,
            'duration': duration,
            'confirmed': confirmed,
            'wemos_open': open_resp[-300:],
            'wemos_close': close_resp[-300:],
        }
    except Exception as e:
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        return {'success': False, 'error': str(e)}


def do_close(port):
    """Handle an immediate close command (no prior open). Used by closeServo()."""
    try:
        close_resp = _send_cmd(
            port, "CLOSE", wait_time=30,
            response_match=lambda r: ('POSITION:' in r) or ('Unknown command' in r),
        )
        print(f'[{ts()}] WEMOS replied to CLOSE: {close_resp!r}', flush=True)
        # Keep the persistent connection open - INSTANT ang susunod na command
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        return {'success': True}
    except Exception as e:
        if os.path.exists(ANIM_FILE):
            os.remove(ANIM_FILE)
        return {'success': False, 'error': str(e)}


# ============== MAIN LOOP ==============
def main():
    # Single-instance guard: a second bridge would fight over the COM port
    # and cause "Access is denied" failures on every feed.
    if not acquire_instance_lock():
        sys.exit(0)

    port = sys.argv[1] if len(sys.argv) > 1 else find_port()
    if port is None:
        print("ERROR: No WEMOS found!", flush=True)
        sys.exit(1)
    
    print(f'[{time.strftime("%H:%M:%S")}] Unified Bridge ready ({port})', flush=True)
    print(f'[{time.strftime("%H:%M:%S")}] Sensor interval: {SENSOR_INTERVAL}s', flush=True)

    # Stale animation file cleanup: kapag may natirang animation_trigger.json
    # mula sa nakaraang run (namatay/kinansel ang bridge sa gitna ng feeding),
    # BURAHIN ito agad - kung hindi, magpapakita agad ng "Dispensing Feed"
    # animation ang browser kahit walang nangyayaring feeding.
    if os.path.exists(ANIM_FILE):
        try:
            os.remove(ANIM_FILE)
            print(f'[{ts()}] Removed stale animation_trigger.json from previous run', flush=True)
        except OSError:
            pass
    
    last_sensor_read = 0
    last_food_level_read = 0
    
    try:
        while True:
            try:
                cmd = read_pending_command()
                if cmd is not None:
                    action = cmd.get('action', 'trigger')

                    if action == 'trigger':
                        duration = int(cmd.get('duration', 5))
                        cage = int(cmd.get('cage', 1))
                        feed_type = cmd.get('type', 'manual')
                        print(f'[{time.strftime("%H:%M:%S")}] Feeding: {duration}s', flush=True)
                        result = do_trigger(port, duration, cage, feed_type)
                        with open(RESULT_FILE, 'w') as f:
                            json.dump({**result, 'port': port, 'timestamp': time.time()}, f)
                        print(f'[{time.strftime("%H:%M:%S")}] {"Done" if result["success"] else "Failed"}', flush=True)

                    elif action == 'close':
                        print(f'[{time.strftime("%H:%M:%S")}] CLOSE command received', flush=True)
                        result = do_close(port)
                        with open(RESULT_FILE, 'w') as f:
                            json.dump({**result, 'port': port, 'timestamp': time.time()}, f)
                        print(f'[{time.strftime("%H:%M:%S")}] {"Closed" if result["success"] else "Failed"}', flush=True)

                    elif action == 'sensor':
                        temp, hum = read_sensors(port)
                        result = {'success': temp is not None, 'data': {'temperature': temp, 'humidity': hum}}
                        with open(RESULT_FILE, 'w') as f:
                            json.dump({**result, 'port': port, 'timestamp': time.time()}, f)

                    elif action == 'status':
                        try:
                            status = _send_cmd(port, "STATUS", wait_time=8)
                            result = {'success': True, 'status': status}
                        except Exception as e:
                            result = {'success': False, 'error': str(e)}
                        with open(RESULT_FILE, 'w') as f:
                            json.dump({**result, 'port': port, 'timestamp': time.time()}, f)

                current_time = time.time()
                if current_time - last_sensor_read >= SENSOR_INTERVAL:
                    temp, hum, _ = read_sensors(port)
                    if temp is not None and hum is not None:
                        result = post_to_api(temp, hum)
                        if result.get('success'):
                            print(f'[{time.strftime("%H:%M:%S")}] DHT22: {temp}C, {hum}%', flush=True)
                        else:
                            print(f'[{time.strftime("%H:%M:%S")}] Sensor API error: {result.get("error", "unknown error")}', flush=True)

                    last_sensor_read = current_time

                if current_time - last_food_level_read >= FOOD_LEVEL_INTERVAL:
                    level = read_food_level(port)
                    if level is not None:
                        result = post_food_level(level)
                        if result and result.get('success'):
                            print(f'[{time.strftime("%H:%M:%S")}] Food level: {level}%', flush=True)
                        elif result:
                            print(f'[{time.strftime("%H:%M:%S")}] Food-level API error: {result.get("error", "unknown error")}', flush=True)
                    last_food_level_read = current_time

            except Exception as e:
                print(f'[{ts()}] Error: {e}', flush=True)

            time.sleep(0.1)   # mabilis na command detection ( dati 0.5s )
    finally:
        release_instance_lock()

if __name__ == '__main__':
    main()