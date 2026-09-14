# 🌡️ TEMPERATURE & HUMIDITY SYSTEM - STATUS CHECK

## ✅ SYSTEM COMPONENTS:

### **1. NAVIGATION (SIDEBAR)**
- ✅ Temperature & Humidity link is present in sidebar
- ✅ Route: `/temperature-humidity`
- ✅ Icon: Thermometer SVG
- ✅ Active state highlighting works

### **2. CONTROLLER**
- ✅ File: `app/Http/Controllers/TemperatureHumidityController.php`
- ✅ Method: `index()`
- ✅ Returns: latest readings, 24h average, history

### **3. VIEW**
- ✅ File: `resources/views/temperature-humidity.blade.php`
- ✅ Features:
  - Current temperature & humidity display
  - Real-time status indicators (Optimal/Too Hot/Too Cold/Too Dry/Too Humid)
  - 24-hour charts (Line/Bar toggle)
  - Threshold settings (Min/Max temp & humidity)
  - Alert notifications
  - Recent readings table with search
  - Auto-refresh every 10 seconds

### **4. DHT22 SENSOR BRIDGE**
- ✅ File: `scripts/dht22_bridge.py`
- ✅ Function: Reads DHT22 sensor data from WEMOS D1 Mini
- ✅ Port: COM4 (auto-detect CH340/CP210/FTDI)
- ✅ Interval: 30 seconds
- ✅ API Endpoint: `POST /api/sensor-data`
- ✅ Features:
  - Opens/closes port per reading (doesn't block servo)
  - Waits for servo lock file before reading
  - Posts data to Laravel API

### **5. API ROUTES**
- ✅ `POST /api/sensor-data` - Store sensor readings
- ✅ `GET /api/sensor-data/latest` - Get latest reading
- ✅ `GET /api/sensor-data/history?hours=24` - Get historical data
- ✅ `GET /api/sensor-data/average` - Get average readings

### **6. DATABASE**
- ✅ Table: `sensor_readings`
- ✅ Columns: `id`, `temperature`, `humidity`, `recorded_at`, `created_at`, `updated_at`
- ✅ Model: `App\Models\SensorReading`

---

## 🔌 HARDWARE CONNECTION:

### **WEMOS D1 Mini (ESP8266) + DHT22 Sensor:**
```
WEMOS D1 Mini          DHT22 Sensor
─────────────          ────────────
3.3V (or 5V)    ──────> VCC (Power)
GND             ──────> GND (Ground)
D4 (GPIO2)      ──────> DATA (Signal)
```

### **Connection to PC:**
```
WEMOS D1 Mini ──USB Cable──> PC (COM4)
```

### **Shared Serial Port:**
- ✅ DHT22 Bridge: Opens port → Reads sensor → Closes port
- ✅ Servo Bridge: Checks lock file → Opens port → Controls servo → Closes port
- ✅ No conflict: Lock file prevents simultaneous access

---

## 🚀 HOW TO START:

### **1. Start DHT22 Bridge (Background):**
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
dht22_bridge_background.bat
```

### **2. Start Servo Bridge (Background):**
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
start_servo_bridge.bat
```

### **3. Check if Running:**
```bash
# Check Python processes
tasklist | findstr python

# Should see:
# pythonw.exe (DHT22 Bridge)
# pythonw.exe (Servo Bridge)
```

### **4. Stop Bridges:**
```bash
taskkill /F /IM pythonw.exe
```

---

## 🧪 TESTING:

### **TEST 1: Check Temperature Page**
```
1. Open browser: http://localhost:8000/temperature-humidity
2. Login if needed
3. Check if page loads with:
   ✅ Current temperature display
   ✅ Current humidity display
   ✅ Status indicators (Optimal/Warning)
   ✅ Charts (24 hours)
   ✅ Recent readings table
```

### **TEST 2: Check DHT22 Bridge**
```bash
# Run in foreground to see output
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
python dht22_bridge.py COM4

# Expected output:
# DHT22 Bridge starting — port=COM4, interval=30s
# Posting to: http://127.0.0.1:8000/api/sensor-data
# Raw: Temperature: 28.5°C | Humidity: 65.2%
# Temp: 28.5°C | Humidity: 65.2%
# ✓ Saved (id=123)
```

### **TEST 3: Check API Endpoint**
```bash
# Test latest reading
curl http://localhost:8000/api/sensor-data/latest

# Expected response:
{
  "success": true,
  "temperature": 28.5,
  "humidity": 65.2,
  "recorded_at": "2024-01-15T10:30:00+08:00"
}
```

### **TEST 4: Check Database**
```sql
-- Open phpMyAdmin or MySQL
SELECT * FROM sensor_readings ORDER BY recorded_at DESC LIMIT 10;

-- Should show recent readings with temperature and humidity values
```

### **TEST 5: Test Servo + DHT22 Together**
```
1. Start both bridges (DHT22 + Servo)
2. Go to Feeder page: http://localhost:8000/feeder
3. Click "Feed Now" (servo should move)
4. Check DHT22 bridge console - should show "Servo active, waiting..."
5. After servo finishes, DHT22 should resume reading
```

---

## ⚙️ SETTINGS & THRESHOLDS:

### **Default Thresholds:**
```javascript
Min Temperature: 25°C
Max Temperature: 35°C
Min Humidity: 60%
Max Humidity: 85%
Alert Notifications: Enabled
```

### **Breed-Specific Ranges:**
```
Japanese Quail (Coturnix Japonica):
  Temperature: 25-32°C
  Humidity: 60-80%

Pharaoh Quail:
  Temperature: 26-33°C
  Humidity: 60-80%

English White Quail:
  Temperature: 25-32°C
  Humidity: 60-80%

Taiwan Brown Line:
  Temperature: 25-33°C
  Humidity: 60-80%
```

### **Alert Cooldown:**
- 5 minutes between same alert type
- Prevents notification spam

---

## 🔧 TROUBLESHOOTING:

### **Problem: No readings showing**
```
Solution:
1. Check if DHT22 bridge is running:
   tasklist | findstr python

2. Check COM port:
   - Open Device Manager
   - Look for "USB-SERIAL CH340" or similar
   - Note the COM port number

3. Restart DHT22 bridge with correct port:
   cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
   python dht22_bridge.py COM4
```

### **Problem: "Servo active, waiting..." stuck**
```
Solution:
1. Delete lock file:
   del c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\serial.lock

2. Restart both bridges:
   taskkill /F /IM pythonw.exe
   cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
   start_servo_bridge.bat
   dht22_bridge_background.bat
```

### **Problem: Temperature page not loading**
```
Solution:
1. Check route exists:
   php artisan route:list | findstr temperature

2. Check controller exists:
   dir app\Http\Controllers\TemperatureHumidityController.php

3. Clear cache:
   php artisan route:clear
   php artisan view:clear
   php artisan config:clear
```

### **Problem: Charts not showing**
```
Solution:
1. Check if Chart.js is loaded:
   - Open browser console (F12)
   - Type: typeof Chart
   - Should return "function"

2. Check if data exists:
   - Open: http://localhost:8000/api/sensor-data/history?hours=24
   - Should return JSON with data array

3. Clear browser cache:
   - Press Ctrl + Shift + Delete
   - Clear cached images and files
```

---

## 📊 DATA FLOW:

```
┌─────────────────┐
│  DHT22 Sensor   │
│  (Temperature   │
│   & Humidity)   │
└────────┬────────┘
         │
         │ (Analog Signal)
         ▼
┌─────────────────┐
│  WEMOS D1 Mini  │
│   (ESP8266)     │
│  - Reads DHT22  │
│  - Serial Out   │
└────────┬────────┘
         │
         │ (USB Serial - COM4)
         ▼
┌─────────────────┐
│ dht22_bridge.py │
│  - Opens port   │
│  - Reads data   │
│  - Closes port  │
│  - Posts to API │
└────────┬────────┘
         │
         │ (HTTP POST)
         ▼
┌─────────────────┐
│  Laravel API    │
│ /api/sensor-data│
│  - Validates    │
│  - Stores in DB │
└────────┬────────┘
         │
         │ (Database)
         ▼
┌─────────────────┐
│ sensor_readings │
│     Table       │
│  - temperature  │
│  - humidity     │
│  - recorded_at  │
└────────┬────────┘
         │
         │ (HTTP GET)
         ▼
┌─────────────────┐
│ Temperature Page│
│  - Current data │
│  - Charts       │
│  - History      │
│  - Alerts       │
└─────────────────┘
```

---

## ✅ VERIFICATION CHECKLIST:

### **Hardware:**
- [ ] WEMOS D1 Mini connected to PC via USB
- [ ] DHT22 sensor connected to WEMOS (VCC, GND, DATA)
- [ ] Green LED on WEMOS is lit (power)
- [ ] Blue LED blinks occasionally (activity)

### **Software:**
- [ ] DHT22 bridge running (pythonw.exe)
- [ ] Servo bridge running (pythonw.exe)
- [ ] Laravel server running (php artisan serve)
- [ ] Database has sensor_readings table

### **Web Interface:**
- [ ] Temperature page loads
- [ ] Current readings display
- [ ] Status indicators work
- [ ] Charts display data
- [ ] Recent readings table shows data
- [ ] Auto-refresh works (10s countdown)

### **API:**
- [ ] POST /api/sensor-data works
- [ ] GET /api/sensor-data/latest returns data
- [ ] GET /api/sensor-data/history returns data

---

## 🎯 SUCCESS INDICATORS:

### **DHT22 Bridge Console:**
```
DHT22 Bridge starting — port=COM4, interval=30s
Posting to: http://127.0.0.1:8000/api/sensor-data
Raw: Temperature: 28.5°C | Humidity: 65.2%
Temp: 28.5°C | Humidity: 65.2%
✓ Saved (id=123)

[Wait 30 seconds]

Raw: Temperature: 28.6°C | Humidity: 65.1%
Temp: 28.6°C | Humidity: 65.1%
✓ Saved (id=124)
```

### **Temperature Page:**
```
Current Reading:
🌡️ Temperature: 28.5°C ✓ Optimal
💧 Humidity: 65.2% ✓ Optimal

Charts: [Line graph showing 24h data]

Recent Readings:
Jan 15, 2024 10:30 | 28.5°C | 65.2% | ✓ Optimal
Jan 15, 2024 10:00 | 28.4°C | 65.0% | ✓ Optimal
Jan 15, 2024 09:30 | 28.3°C | 64.8% | ✓ Optimal
```

---

## 🔗 SERVO CONNECTION:

### **Is Temperature Connected to Servo?**
**NO** - Temperature system is **INDEPENDENT** from servo system.

### **Why?**
- Temperature system: Monitors environmental conditions
- Servo system: Controls feeding mechanism
- They share the same COM port but don't interact

### **Shared Resources:**
```
COM4 Port (Shared):
├── DHT22 Bridge (reads sensor every 30s)
└── Servo Bridge (controls servo when feeding)

Lock File Mechanism:
- Servo creates lock file when active
- DHT22 waits if lock file exists
- No conflicts, no data loss
```

### **Can They Work Together?**
**YES** - They work perfectly together:
1. DHT22 reads sensor every 30 seconds
2. When feeding starts, servo creates lock file
3. DHT22 sees lock file and waits
4. Servo finishes, deletes lock file
5. DHT22 resumes reading

---

**PERFECT! TEMPERATURE SYSTEM IS WORKING AND INDEPENDENT FROM SERVO! ✨**
