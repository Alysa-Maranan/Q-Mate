# WEMOS D1 R1 Firmware Upload Guide

## Current Status
- ✅ Python script is fixed and working
- ✅ pyserial installed in system Python
- ✅ COM4 detected with WEMOS board
- ❌ **Firmware NOT uploaded** - board sending null bytes

## Step-by-Step Upload Instructions

### 1. Download Arduino IDE
- Go to: https://www.arduino.cc/en/software
- Download: Arduino IDE 2.x (latest version)
- Install it

### 2. Add ESP8266 Support to Arduino IDE
1. Open **Arduino IDE**
2. Go to **File** → **Preferences**
3. Find "Additional Board Manager URLs" field
4. Add this URL: `https://arduino.esp8266.com/stable/package_esp8266com_index.json`
5. Click **OK**
6. Go to **Tools** → **Board Manager**
7. Search for: `ESP8266`
8. Click **Install** on "esp8266 by ESP8266 Community" (version 3.0.0+)
9. Wait for install to complete (~200MB)

### 3. Select Board in Arduino IDE
1. **Tools** → **Board** → **ESP8266 Boards** → Select **LOLIN(WEMOS) D1 R1**
2. **Tools** → **Port** → Select **COM4**

### 4. Upload Firmware
1. Open file: `scripts/servo_dht22_combined.ino`
2. Click **Upload** button (or Ctrl+U)
3. Arduino will auto-reset the board during upload
4. Wait for: "Upload Complete" message (~30 seconds)

### 5. Verify Upload
After upload, the board will restart. Open Tools → Serial Monitor and you should see:

```
═══════════════════════════════════════
  WEMOS D1 R1 - Servo + DHT22 Ready
═══════════════════════════════════════

COMMANDS:
  TRIGGER [duration] - Open servo for N seconds (default 5)
  STATUS - Show servo & sensor status
  OPEN - Open servo
  CLOSE - Close servo
  TEMP - Read temperature from DHT22
  HUMIDITY - Read humidity from DHT22
  SENSOR - Read all sensor data
═══════════════════════════════════════
```

### 6. Test with Python Script
```bash
C:\Users\maran\AppData\Local\Programs\Python\Python313\python.exe scripts/servo_dht22_web.py sensor 5 COM4
```

Should return:
```json
{
  "success": true,
  "command": "SENSOR",
  "response": "Temperature: 28.5 °C | Humidity: 55.2 %",
  "data": {
    "temperature": 28.5,
    "humidity": 55.2
  }
}
```

### 7. Test Servo Movement
```bash
C:\Users\maran\AppData\Local\Programs\Python\Python313\python.exe scripts/servo_dht22_web.py trigger 3 COM4
```

Servo should move/activate for 3 seconds.

## Troubleshooting

### "No COM port showing"
- Check USB cable connection
- Try different USB port
- Install CH340 driver: https://sparks.gogo.co.nz/ch340.html

### "Failed to upload"
- Check board is LOLIN(WEMOS) D1 R1 selected
- Try lower baud rate: Tools → Upload Speed → 115200
- Press reset button on board during upload

### "Upload timeout"
- Disconnect any other serial monitors
- Close the diagnostic script if running
- Try again

### Board still sending null bytes after upload
- Board may not be in correct position during upload
- Hold reset button for 2 seconds
- Try upload again

## File Locations
- Python script: `scripts/servo_dht22_web.py`
- Firmware: `scripts/servo_dht22_combined.ino`
- Diagnostic tool: `scripts/diagnose_wemos.py`

## Next Steps
Once firmware is uploaded successfully:
1. Test at: `/feeder/trigger-servo` (web)
2. Test at: `/feeder/read-sensors` (web)
3. Check Laravel logs: `storage/logs/laravel.log`
