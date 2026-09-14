# WEMOS D1 R1 HARDWARE SETUP GUIDE
# Complete System: Servo + DHT22 + Ultrasonic Sensor

---

## 📋 KAILANGAN MO (HARDWARE):

### 1. WEMOS D1 R1 Board
   - ESP8266-based microcontroller
   - USB-powered (5V from USB or external power)

### 2. MG996R Servo Motor
   - 3 wires: Signal (Orange/Yellow), Power (Red), Ground (Brown/Black)
   - Kailangan ng **external 5-6V power supply** (hindi sapat ang USB power)

### 3. DHT22 Temperature & Humidity Sensor
   - 3 pins: VCC, Data, GND
   - Kailangan ng **10kΩ pull-up resistor** sa VCC at Data (kung wala sa module)

### 4. HC-SR04 Ultrasonic Sensor
   - 4 pins: VCC, TRIG, ECHO, GND
   - May built-in LEDs na nagso-show kung working

### 5. External Power Supply (IMPORTANT!)
   - **5V 2A minimum** para sa MG996R Servo
   - Ito ang dahilan kung bakit hindi gumagana ang servo sa USB lang!

---

## 🔌 WIRING DIAGRAM:

```
WEMOS D1 R1 PIN LAYOUT:
                    ┌─────────────────┐
                    │      USB        │
                    │                 │
              RST   │ ●               │
              A0    │ ●               │
              D0    │ ●               │
              D5    │ ●  ← Servo Signal│
              D6    │ ●  ← DHT22 Data │
              D7    │ ●  ← TRIG (Ultra)│
              D8    │ ●               │
              3.3V  │ ●  ← DHT22 VCC  │
              GND   │ ●  ← COMMON GND │
                    │                 │
              5V    │ ●  ← HC-SR04 VCC│
              D4    │ ●               │
              D3    │ ●               │
              D2    │ ●  ← ECHO (Ultra)│
              D1    │ ●               │
              RX    │ ●               │
              TX    │ ●               │
                    └─────────────────┘
```

---

## 🔧 DETAILED WIRING:

### MG996R Servo Motor:
```
Servo Wire        →  WEMOS Connection
─────────────────────────────────────
Orange (Signal)   →  D5 (GPIO14)
Red (Power +)     →  External 5V Power Supply (+)
Brown (Ground)    →  WEMOS GND + Power Supply GND (COMMON GROUND!)
```

### DHT22 Sensor:
```
DHT22 Pin         →  WEMOS Connection
─────────────────────────────────────
VCC (Pin 1)       →  3.3V
Data (Pin 2)      →  D6 (GPIO12) + 10kΩ pull-up to 3.3V
GND (Pin 4)       →  GND
```
*Note: Kung module na yung DHT22 (may board na), usually may built-in pull-up na*

### HC-SR04 Ultrasonic Sensor:
```
HC-SR04 Pin       →  WEMOS Connection
─────────────────────────────────────
VCC               →  5V
TRIG              →  D7 (GPIO13)
ECHO              →  D2 (GPIO4)
GND               →  GND
```

### External Power Supply (IMPORTANT!):
```
External 5V Supply:
  (+) → Servo Red wire
  (-) → WEMOS GND (COMMON GROUND with WEMOS!)
```

---

## ⚠️ IMPORTANT NOTES:

1. **COMMON GROUND** - Dapat connected lahat ng GND (WEMOS, Servo, DHT22, HC-SR04, Power Supply)
2. **External Power** - Ang MG996R ay kumukonsumo ng hanggang 2A kapag naglo-load, hindi sapat ang USB
3. **3.3V vs 5V** - Ang WEMOS ay 3.3V logic. Ang DHT22 ay okay sa 3.3V, pero ang HC-SR04 ay kailangan ng 5V
4. **Servo Noise** - Maglagay ng 100µF capacitor sa power supply para sa noise filtering


## 🚀 SETUP STEPS:

### Step 1: Install Arduino IDE at ESP8266 Board
1. Download Arduino IDE: https://www.arduino.cc/en/software
2. Open Arduino IDE → File → Preferences
3. Sa "Additional Board Manager URLs" lagyan ng:
   ```
   http://arduino.esp8266.com/stable/package_esp8266com_index.json
   ```
4. Tools → Board → Board Manager → Search "ESP8266" → Install
5. Select Board: "LOLIN(WEMOS) D1 R1"

### Step 2: Install Required Libraries
Sa Arduino IDE:
1. Sketch → Include Library → Manage Libraries
2. Search at install:
   - "DHT sensor library" by Adafruit
   - "Adafruit Unified Sensor" (dependency)
   - "Servo" (built-in)

### Step 3: Upload the Code
1. Open `arduino/WEMOS/WEMOS.ino` sa Arduino IDE
2. Connect WEMOS via USB
3. Select correct COM port (Tools → Port)
4. Click Upload button

### Step 4: Test sa Serial Monitor
1. Open Serial Monitor (Tools → Serial Monitor)
2. Set baud rate to **9600**
3. Send commands:
   - `STATUS` - Para makita lahat ng sensor readings
   - `SENSOR` - Para sa DHT22 lang
   - `ULTRASONIC` - Para sa Ultrasonic lang
   - `TRIGGER 5` - Para i-open servo for 5 seconds
   - `OPEN` - Para i-open servo
   - `CLOSE` - Para i-close servo

---

## 💻 PYTHON SETUP:

### Step 1: Install Python Dependencies
```bash
pip install pyserial
```

### Step 2: Run the Hardware Bridge
```bash
cd C:\xampp\htdocs\CAPSTONE SQUIFM
python scripts\hardware_bridge_complete.py
```

### Step 3: Test Commands
Gawa ng test JSON file:
```bash
echo {"action":"trigger","duration":3,"cage":"1","type":"manual"} > storage\logs\servo_command.json
```

Para sa sensor reading:
```bash
echo {"action":"sensor"} > storage\logs\servo_command.json
```

Para sa full status:
```bash
echo {"action":"status"} > storage\logs\servo_command.json
```

---

## 🛠️ TROUBLESHOOTING:

### Hindi makita ang WEMOS?
- Check USB cable (dapat data cable, hindi charging-only)
- Install CH340/CP2102 driver: https://docs.wemos.cc/en/latest/ch340_driver.html
- Check Device Manager para sa COM port

### Servo hindi gumagalaw?
- Check external power supply (5V 2A minimum)
- Check COMMON GROUND connection
- Check signal wire sa D1

### DHT22 walang reading?
- Check wiring (VCC, Data, GND)
- Check 10kΩ pull-up resistor
- Subukan mag-add ng delay sa code kung mabagal

### Ultrasonic walang reading?
- Check TRIG sa D5, ECHO sa D6
- Check 5V power
- Check kung may obstacle sa harap (minimum 2cm)

### Communication Error?
- Siguraduhin na 9600 baud ang Serial Monitor
- Check kung may ibang program na gumagamit ng COM port
- I-restart ang WEMOS at Python script

---

## 📞 QUICK REFERENCE:

| Command     | Description                    |
|-------------|--------------------------------|
| `OPEN`      | Open servo to feed position    |
| `CLOSE`     | Close servo to rest position   |
| `TRIGGER 5` | Open for 5 seconds then close  |
| `SENSOR`    | Read DHT22 temperature/humidity|
| `ULTRASONIC`| Read food level (%) from HC-SR04     |
| `STATUS`    | Full system status report      |

---

**Created for SQUIFM - Quail Farm Management System**