# Wemos D1 TX + Servo Motor Setup Guide

## Hardware Connection

### Wemos D1 TX Pinout
```
Wemos D1 TX (Top View)
┌─────────────────────────────────┐
│ A0  D0  D1  D2  D3  D4  D5  D6  │
│ GND RST TX  RX  5V  GND 3V3 GND │
│      ↑   ↑   ↑                  │
│      |   |   └─ RX (GPIO3)      │
│      |   └───── TX (GPIO1)      │
│      └───────── RST             │
│                                 │
│ D7  D8  D9  D10 D11 D12 D13 D14 │
│ GND GND GND GND GND GND GND GND │
└─────────────────────────────────┘
```

### Servo Motor Connection
**Servo has 3 wires:**
- **Red** = 5V Power
- **Brown/Black** = GND (Ground)
- **Yellow/Orange** = Signal (PWM)

### Recommended Wiring

| Servo Wire | Wemos D1 TX Pin | Notes |
|-----------|-----------------|-------|
| Red (5V) | 5V | Power supply (EXTERNAL) |
| Brown/Black (GND) | GND | Ground |
| Yellow/Orange (Signal) | D8 (GPIO15) | PWM signal pin |

**Why D8 and not D1 TX?**
- D1 TX (GPIO1) is used for Serial communication with Python script
- D8 (GPIO15) is free and works great for servo PWM

**Alternative pins for servo signal:**
- D8 (GPIO15) - **RECOMMENDED**
- D3 (GPIO0)
- D4 (GPIO2)
- D5 (GPIO14)
- D6 (GPIO12)
- D7 (GPIO13)

## Arduino Code for Wemos D1 TX

Upload this code to your Wemos D1 TX:

```cpp
#include <Arduino.h>
#include <Servo.h>

// Pin configuration
const int SERVO_PIN = D8;  // GPIO15 - Use D8 for servo
const int REST_POS = 0;    // Closed position (0 degrees)
const int FEED_POS = 90;   // Open position (90 degrees)

Servo myServo;

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  // Initialize servo
  myServo.attach(SERVO_PIN);
  myServo.write(REST_POS);
  
  Serial.println("\n\nWemos D1 TX Servo Ready");
  Serial.println("Commands:");
  Serial.println("  TRIGGER [duration] - Open servo for N seconds (default 5)");
  Serial.println("  STATUS - Show current servo position");
  Serial.println("  OPEN - Open servo");
  Serial.println("  CLOSE - Close servo");
}

void handleTrigger(int durationSeconds) {
  // Validate duration
  if (durationSeconds < 1) durationSeconds = 1;
  if (durationSeconds > 60) durationSeconds = 60;
  
  // Open servo
  myServo.write(FEED_POS);
  Serial.print("Servo OPEN for ");
  Serial.print(durationSeconds);
  Serial.println(" seconds");
  
  // Hold open for specified duration
  delay(durationSeconds * 1000);
  
  // Close servo
  myServo.write(REST_POS);
  Serial.println("Servo CLOSED");
  Serial.println("OK");
}

void loop() {
  if (Serial.available()) {
    String line = Serial.readStringUntil('\n');
    line.trim();
    
    if (line.length() == 0) return;
    
    if (line.startsWith("TRIGGER")) {
      int spacePos = line.indexOf(' ');
      int duration = 5; // Default 5 seconds
      
      if (spacePos > 0) {
        String numStr = line.substring(spacePos + 1);
        duration = numStr.toInt();
      }
      
      handleTrigger(duration);
    }
    else if (line == "OPEN") {
      myServo.write(FEED_POS);
      Serial.println("Servo OPEN");
    }
    else if (line == "CLOSE") {
      myServo.write(REST_POS);
      Serial.println("Servo CLOSED");
    }
    else if (line == "STATUS") {
      Serial.print("Servo position: ");
      Serial.println(myServo.read());
    }
    else {
      Serial.println("Unknown command");
    }
  }
}
```

## Installation Steps

### 1. Install Arduino IDE
- Download from: https://www.arduino.cc/en/software

### 2. Add Wemos D1 TX Board Support
1. Open Arduino IDE
2. Go to **File → Preferences**
3. In "Additional Boards Manager URLs", add:
   ```
   http://arduino.esp8266.com/stable/package_esp8266com_index.json
   ```
4. Click **Tools → Board → Boards Manager**
5. Search for "esp8266" and install "esp8266 by ESP8266 Community"

### 3. Select Board and Port
1. **Tools → Board → ESP8266 Boards → LOLIN(WEMOS) D1 R1 mini**
2. **Tools → Port → COM3** (or your USB port - check Device Manager)
3. **Tools → Upload Speed → 115200**
4. **Tools → CPU Frequency → 80 MHz**

### 4. Upload Code
1. Copy the Arduino code above
2. Paste into Arduino IDE
3. Click **Upload** (→ button)
4. Wait for "Done uploading" message

### 5. Test Connection
1. Open **Tools → Serial Monitor**
2. Set baud rate to **115200**
3. Type commands:
   - `TRIGGER 5` - Open servo for 5 seconds
   - `OPEN` - Open servo
   - `CLOSE` - Close servo
   - `STATUS` - Show servo position

## Website Integration

Your Laravel app is already configured! Here's how it works:

### 1. Schedule Feeding
- Go to **Dashboard → Feeder → Schedule**
- Set time and days
- Website automatically triggers servo at scheduled time

### 2. Manual Feeding
- Go to **Dashboard → Feeder → Manual Feed**
- Enter duration (seconds)
- Click "Feed Now"
- Servo opens for specified duration

### 3. Configuration in `.env`
```
SERVO_SERIAL_PORT=COM4
PYTHON_PATH=python
FEEDER_MODE=serial
```

Change `COM4` to your actual USB port if different.

## ⚠️ CRITICAL: Power Supply Setup

**Your D1 TX board MUST have external 5V power for servo!**

### Why?
- USB provides max 500mA
- Servo needs 800mA-1000mA when moving
- Without external power: servo won't move or board will reset

### External Power Setup (REQUIRED)
```
5V Power Supply (2A minimum)
├─ Red (+5V) → Servo Red + Wemos 5V pin
├─ Black (GND) → Servo Black + Wemos GND + USB GND (common ground)
└─ (Wemos USB still connected for programming/serial)
```

### Wiring Diagram
```
┌─────────────────┐
│  5V Power Supply│
│  (2A minimum)   │
└────┬────────┬───┘
     │        │
    Red      Black
     │        │
     ├────────┼──────────┐
     │        │          │
     │        │      ┌───┴────┐
     │        │      │ Wemos  │
     │        │      │ D1 TX  │
     │        │      │        │
     ├────────┼──────┤ 5V     │
     │        │      │ GND    │
     │        │      │ D8     │
     │        │      └────┬───┘
     │        │           │
     │        │      ┌────┴────┐
     │        │      │  Servo  │
     │        │      │         │
     └────────┼──────┤ Red     │
              │      │ Black   │
              └──────┤ Yellow  │
                     └─────────┘
```

## Troubleshooting

### Servo not responding
1. Check USB cable connection
2. Verify COM port in Device Manager
3. Check Arduino code uploaded successfully
4. Open Serial Monitor and test commands manually
5. **Check external power supply is connected**

### Servo moves but then stops
- **This means power issue!** External 5V supply not connected properly
- Check all power connections
- Use multimeter to verify 5V at servo connector

### Wrong COM port
1. Open Device Manager
2. Look for "USB-SERIAL CH340" or similar
3. Note the COM number
4. Update `.env` file: `SERVO_SERIAL_PORT=COM3` (or your port)

### Servo moves wrong direction
1. Change `REST_POS` and `FEED_POS` values in Arduino code
2. Example: `const int REST_POS = 90;` and `const int FEED_POS = 0;`

### Python script not found
1. Ensure `scripts/servo_control.py` exists
2. Install Python: https://www.python.org/downloads/
3. Install pyserial: `pip install pyserial`

## Testing Checklist

- [ ] External 5V power supply connected (CRITICAL!)
- [ ] Wemos D1 TX board selected in Arduino IDE
- [ ] Correct COM port selected
- [ ] Code uploaded successfully
- [ ] Serial Monitor shows "Wemos D1 TX Servo Ready"
- [ ] `TRIGGER 5` command works in Serial Monitor
- [ ] Servo opens and closes correctly
- [ ] `.env` file has correct `SERVO_SERIAL_PORT`
- [ ] Website schedule/manual feed triggers servo

## Next Steps

1. **Connect external 5V power supply** (MOST IMPORTANT!)
2. **Test servo manually** via Serial Monitor
3. **Test website integration** with manual feed button
4. **Create test schedule** for automatic feeding
5. **Monitor logs** in `storage/logs/laravel.log`

Good luck! 🎉
