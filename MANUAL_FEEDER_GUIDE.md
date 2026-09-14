# MANUAL FEEDER SETUP GUIDE

## PROBLEMA
Hindi gumagana ang servo motor sa Manual Feeder page.

## SOLUSYON

### STEP 1: I-run ang Servo Bridge (KAILANGAN ITO!)

Buksan ang **Command Prompt** at i-run:

```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
START_SERVO.bat
```

O kaya:

```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
pythonw servo_bridge.py COM4
```

**IMPORTANTE:** Dapat LAGING RUNNING ang servo_bridge.py para gumana ang manual feeder!

### STEP 2: I-check kung running ang servo bridge

```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM
check_servo_bridge.bat
```

Dapat makita mo: "Servo bridge is RUNNING"

### STEP 3: I-test ang Manual Feeder

1. Buksan ang browser: `http://localhost/feeder`
2. Click ang **Manual** tab
3. Piliin ang duration (5, 10, 15, o 30 seconds)
4. Piliin ang cage number (1 o 2)
5. Click **Activate Feed**

### KUNG HINDI PA RIN GUMAGANA:

#### Check 1: Arduino/ESP32 nakaconnect ba?
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
python diagnose_wemos.py
```

#### Check 2: Tama ba ang COM port?
- Buksan ang Device Manager
- Tingnan ang "Ports (COM & LPT)"
- Hanapin ang CH340 o USB-SERIAL device
- I-note ang COM port number (e.g., COM4, COM3)

#### Check 3: I-update ang COM port sa .env
```
SERVO_SERIAL_PORT=COM4
```

#### Check 4: I-test directly ang servo
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
python servo_control.py trigger 5 COM4
```

## PAANO GUMAGANA

1. **Manual Feeder Form** → Sends AJAX request to `/feeder/manual/feed`
2. **FeedingScheduleController** → Creates `servo_command.json` file
3. **servo_bridge.py** → Reads command file and sends signal to Arduino/ESP32
4. **Arduino/ESP32** → Moves servo motor
5. **Browser** → Shows success message

## IMPORTANTE

- **LAGING I-RUN ANG START_SERVO.bat** bago gumamit ng manual feeder
- Kung nag-restart ang computer, i-run ulit ang START_SERVO.bat
- Kung nag-disconnect ang Arduino/ESP32, i-restart ang servo_bridge.py

## AUTO-START (OPTIONAL)

Para automatic na mag-start ang servo bridge:

1. Press `Win + R`
2. Type: `shell:startup`
3. Copy ang `START_SERVO.bat` doon
4. Restart ang computer

## TROUBLESHOOTING

### Error: "Could not open port COM4"
- Check kung nakaconnect ang Arduino/ESP32
- Try ibang COM port (COM3, COM5, etc.)

### Error: "Python not found"
- Install Python: https://www.python.org/downloads/
- I-check: `python --version`

### Error: "Module 'serial' not found"
- Install pyserial: `pip install pyserial`

### Servo hindi gumagalaw pero walang error
- Check kung may power ang servo motor
- Check kung tama ang wiring (Signal, VCC, GND)
- I-test ang servo sa Arduino IDE Serial Monitor

## LOGS

Tingnan ang logs para sa debugging:
```
c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\laravel.log
c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\servo_command.json
c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\servo_result.json
```
