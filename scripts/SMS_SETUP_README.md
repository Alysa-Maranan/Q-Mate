# Fish Feeder SMS Notification Setup Guide

## Hardware Setup

### Required Components:
1. **ESP32** - Servo motor control (already working)
2. **Arduino** (Uno/Nano/Mega) - GSM SMS sender
3. **SIM900A GSM Module** - SMS sending
4. **Servo Motor** - Fish feeder gate
5. **Power Supplies:**
   - ESP32: USB 5V
   - Arduino: USB 5V
   - SIM900A: 9-12V 2A adapter (recommended) OR share Arduino 5V (risky)
6. **SIM Card** with load/credits

---

## Wiring Diagram

### ESP32 Connections (as before):
```
Servo Motor → ESP32
- Signal (Orange) → GPIO 13
- VCC (Red) → 5V
- GND (Brown) → GND

USB → PC (COM4)
```

### Arduino + SIM900A Connections:
```
SIM900A → Arduino
- TX → Arduino Pin 3 (RX)
- RX → Arduino Pin 2 (TX)
- GND → Arduino GND

SIM900A → External Power (RECOMMENDED)
- VCC/VIN → 9-12V 2A adapter (+)
- GND → 9-12V adapter (-) AND Arduino GND (common ground!)

Arduino → PC via USB (COM3)
```

**⚠️ IMPORTANT:** SIM900A needs high current when sending SMS! Use external power or risk Arduino restarts.

---

## Software Setup

### Step 1: Upload Arduino Code
1. Open Arduino IDE
2. Open `scripts/arduino_gsm.ino`
3. Select Board: Arduino Uno/Nano/Mega (your model)
4. Select Port: COM3 (or whatever Arduino is on)
5. **Upload** ✅
6. Open Serial Monitor (9600 baud)
7. Should see: **"Arduino GSM Ready"**

### Step 2: Configure .env File
Edit `my-app/.env` and set:

```env
# ESP32 Servo Configuration
SERVO_SERIAL_PORT=COM4
PYTHON_PATH=python

# GSM SIM900A Configuration  
GSM_SERIAL_PORT=COM3
SMS_PHONE_NUMBER=+639123456789
```

**⚠️ Change +639123456789 to your actual phone number!**
- Format: **+63** (country code) + **9** + **9-digit number**
- Example: +639171234567

### Step 3: Test SMS Sending

**Test 1: Direct Arduino Test**
1. Open Arduino Serial Monitor (9600 baud)
2. Type: `TEST` → Should reply "Arduino GSM is working!"
3. Type: `SMS|+639123456789|Hello Test`
4. Wait ~10-15 seconds
5. Check your phone for SMS ✅

**Test 2: Python Script Test**
```powershell
cd scripts
python sms_sender.py +639123456789 "Test from Python" COM3
```

Expected output:
```
Connected to Arduino on COM3
Sending: SMS|+639123456789|Test from Python
✅ SMS sent successfully!
```

### Step 4: Test Complete System

**Manual Feed with SMS:**
1. Go to web → Feeder → Manual tab
2. Set duration: 5 seconds
3. Click "Activate Feed"
4. **Result:**
   - Servo moves ✅
   - Phone receives SMS: "Manual feeding completed: 5 cycles" ✅

**Scheduled Feed with SMS:**
1. Add schedule (2 minutes from now, 10 seconds)
2. Wait for scheduled time
3. **Result:**
   - Servo moves automatically ✅
   - Status shows "feeding_now" → "done_feeding" ✅
   - Phone receives SMS: "Fish fed at 15:30:00 for 10 seconds" ✅

---

## System Architecture

```
┌──────────────┐
│  Laravel/PC  │
└──────┬───────┘
       │
       ├─────────────────┬─────────────────┐
       │                 │                 │
       ▼                 ▼                 ▼
┌──────────┐      ┌──────────┐     ┌──────────┐
│  ESP32   │      │ Arduino  │     │ Database │
│  (COM4)  │      │  (COM3)  │     │          │
└────┬─────┘      └────┬─────┘     └──────────┘
     │                 │
     ▼                 ▼
┌──────────┐      ┌──────────┐
│  Servo   │      │ SIM900A  │
│  Motor   │      │   GSM    │
└──────────┘      └────┬─────┘
                       │
                       ▼
                  📱 SMS to User
```

**Flow:**
1. User adds schedule OR clicks manual feed
2. Laravel → Python script
3. Python → ESP32 (servo moves)
4. Python → Arduino → GSM (SMS sent)
5. User gets notification! ✅

---

## Troubleshooting

### SMS Not Sending

**Problem:** "SMS Failed!" in Serial Monitor
- **Check:** SIM card has load/credits
- **Check:** SIM card PIN disabled
- **Check:** Network signal (SIM900A LED should blink every 3 seconds = good signal)
- **Try:** `AT+CSQ` in Serial Monitor (signal quality, >10 is good)

**Problem:** Arduino restarts when sending SMS
- **Fix:** Use external 9-12V 2A power supply for SIM900A
- **Temporary:** Reduce USB cable length, use 2A USB charger

**Problem:** No response from Arduino
- **Check:** Correct COM port (COM3)
- **Check:** Arduino code uploaded successfully
- **Check:** 9600 baud rate in Serial Monitor

### Servo Not Moving

**Problem:** Servo doesn't move but SMS sends
- **Check:** ESP32 connected to COM4
- **Check:** Servo wired to GPIO 13
- **Check:** ESP32 code uploaded

---

## Phone Number Format

**Correct formats:**
- ✅ `+639171234567` (with + and country code)
- ✅ `+639123456789`

**Wrong formats:**
- ❌ `09171234567` (missing country code)
- ❌ `639171234567` (missing +)
- ❌ `+63 917 123 4567` (has spaces)

---

## Cost Estimate

- SMS sending: ~₱1 per SMS (depends on your load promo)
- Daily cost (4 feeds/day): ₱4/day = ₱120/month

**Tip:** Use unlimited SMS promos para mas mura! 📱

---

## For Capstone Demo

**Demo Script:**
1. Show hardware setup (ESP32 + Arduino + GSM)
2. Show web interface
3. **Add schedule live**
4. **Wait for feeding time**
5. **Show servo moving** (physical demo)
6. **Show SMS received on phone** (show to panel!)
7. Explain system architecture diagram
8. Show logs/database updates

**Bonus points:**
- Show error handling (what if SMS fails)
- Show dual notifications (web + SMS)
- Explain separation of concerns (servo vs SMS)

---

Good luck sa defense! 🎓🚀
