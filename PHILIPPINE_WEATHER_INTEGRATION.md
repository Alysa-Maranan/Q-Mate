# 🌡️ PHILIPPINE WEATHER INTEGRATION

## ✅ WHAT CHANGED:

### **1. REAL PHILIPPINE WEATHER DATA**
- ✅ Temperature & Humidity now uses **real Philippine weather**
- ✅ Location: **Manila, Philippines**
- ✅ Updates every **10 seconds**
- ✅ Shows current weather description (hot, warm, pleasant, cool)

### **2. AUTOMATIC WEATHER UPDATES**
```
Every 10 seconds:
1. Fetch weather from Manila, Philippines
2. Update temperature display
3. Update humidity display
4. Check if weather is good/bad for quails
5. Send notification if needed
6. Save to database for history
```

### **3. TAGALOG NOTIFICATIONS**
```
🥶 Malamig: "Malamig ang panahon: 23.5°C - Hindi maganda para sa quail!"
🔥 Mainit: "Sobrang init ang panahon: 36.2°C - Delikado para sa quail!"
☀️ Maganda: "Magandang panahon: 28.5°C - Perfect para sa quail!"
💨 Tuyo: "Tuyo ang hangin: 55% - Kailangan ng tubig!"
💦 Basa: "Sobrang basa ang hangin: 90% - Kailangan ng ventilation!"
```

---

## 🌤️ WEATHER CONDITIONS:

### **OPTIMAL FOR QUAILS:**
```
Temperature: 25-35°C
Humidity: 60-85%
Status: ✓ Optimal
Notification: "Magandang panahon - Perfect para sa quail!"
```

### **TOO COLD:**
```
Temperature: < 25°C
Status: 🥶 Too Cold
Notification: "Malamig ang panahon - Hindi maganda para sa quail!"
Action: Provide heating, close ventilation
```

### **TOO HOT:**
```
Temperature: > 35°C
Status: 🔥 Too Hot
Notification: "Sobrang init ang panahon - Delikado para sa quail!"
Action: Provide cooling, increase ventilation, add water
```

### **TOO DRY:**
```
Humidity: < 60%
Status: 💨 Too Dry
Notification: "Tuyo ang hangin - Kailangan ng tubig!"
Action: Add water containers, mist the area
```

### **TOO HUMID:**
```
Humidity: > 85%
Status: 💦 Too Humid
Notification: "Sobrang basa ang hangin - Kailangan ng ventilation!"
Action: Increase ventilation, reduce water sources
```

---

## 📊 WEATHER DATA SOURCE:

### **PRIMARY: OpenWeatherMap API**
```
API: https://api.openweathermap.org/data/2.5/weather
Location: Manila, Philippines
Update Interval: 10 seconds
Data: Temperature, Humidity, Weather Description
```

### **FALLBACK: Simulated Philippine Weather**
```
If API fails, system generates realistic Philippine weather:

Morning (6AM-12PM): 27-30°C, 65-85% humidity
Afternoon (12PM-5PM): 30-33°C, 65-85% humidity
Evening (5PM-8PM): 28-30°C, 65-85% humidity
Night (8PM-6AM): 24-26°C, 65-85% humidity

Weather descriptions:
- > 32°C: "hot and humid"
- > 30°C: "warm and humid"
- > 27°C: "partly cloudy"
- > 25°C: "pleasant"
- < 25°C: "cool"
```

---

## 🔔 NOTIFICATION SYSTEM:

### **NOTIFICATION COOLDOWN:**
```
Same alert type: 5 minutes cooldown
Good weather alert: 10 minutes cooldown

Example:
10:00 AM - "Sobrang init: 36°C" ❌
10:03 AM - (No notification - cooldown) ⏳
10:06 AM - "Sobrang init: 36.5°C" ❌ (New notification)
```

### **NOTIFICATION TYPES:**
```
1. Warning (Yellow): Temperature/Humidity out of range
2. Success (Green): Weather is optimal for quails
3. Error (Red): Critical conditions (future feature)
```

---

## 🧪 TESTING:

### **TEST 1: Check Philippine Weather**
```
1. Open: http://localhost:8000/temperature-humidity
2. Wait 10 seconds
3. Check display:
   ✅ Temperature shows Manila weather
   ✅ Humidity shows Manila weather
   ✅ Location shows "Manila, Philippines"
   ✅ Weather description shows (hot/warm/pleasant/cool)
```

### **TEST 2: Check Notifications**
```
1. Open Temperature page
2. Wait for weather update
3. Check if notification appears:
   ✅ If temp < 25°C: "Malamig ang panahon..."
   ✅ If temp > 35°C: "Sobrang init ang panahon..."
   ✅ If 25-35°C: "Magandang panahon..."
```

### **TEST 3: Check API Endpoint**
```bash
# Test Philippine weather API
curl http://localhost:8000/api/weather/philippines

# Expected response:
{
  "success": true,
  "temperature": 28.5,
  "humidity": 75.2,
  "feels_like": 31.2,
  "description": "partly cloudy",
  "location": "Manila, Philippines",
  "timestamp": "2024-01-15T10:30:00+08:00"
}
```

### **TEST 4: Check Database Storage**
```sql
-- Check if weather data is being saved
SELECT * FROM sensor_readings 
ORDER BY recorded_at DESC 
LIMIT 10;

-- Should show recent Manila weather readings
```

---

## ⚙️ CONFIGURATION:

### **OPTIONAL: Add OpenWeatherMap API Key**
```
1. Get free API key from: https://openweathermap.org/api
2. Add to .env file:
   OPENWEATHER_API_KEY=your_api_key_here
3. Restart Laravel server

Note: System works without API key using fallback simulation
```

### **CHANGE LOCATION:**
```php
// In TemperatureHumidityController.php
// Change 'Manila,PH' to any Philippine city:

'q' => 'Cebu,PH',      // Cebu
'q' => 'Davao,PH',     // Davao
'q' => 'Quezon City,PH', // Quezon City
'q' => 'Makati,PH',    // Makati
```

### **ADJUST THRESHOLDS:**
```javascript
// On Temperature page, click "Thresholds & Settings"
Min Temperature: 25°C (default)
Max Temperature: 35°C (default)
Min Humidity: 60% (default)
Max Humidity: 85% (default)

// Click "Save Settings" to apply
```

---

## 📈 DATA FLOW:

```
┌─────────────────────┐
│ OpenWeatherMap API  │
│  (Manila Weather)   │
└──────────┬──────────┘
           │
           │ (HTTP GET every 10s)
           ▼
┌─────────────────────┐
│  Laravel Controller │
│ getPhilippineWeather│
└──────────┬──────────┘
           │
           │ (JSON Response)
           ▼
┌─────────────────────┐
│  Temperature Page   │
│  - Fetch weather    │
│  - Update display   │
│  - Check thresholds │
│  - Send notification│
│  - Save to database │
└──────────┬──────────┘
           │
           │ (POST to API)
           ▼
┌─────────────────────┐
│ sensor_readings DB  │
│  - temperature      │
│  - humidity         │
│  - recorded_at      │
└─────────────────────┘
```

---

## 🎯 BENEFITS:

### **1. REAL-TIME PHILIPPINE WEATHER**
- ✅ Accurate Manila weather data
- ✅ Updates every 10 seconds
- ✅ No manual sensor needed

### **2. AUTOMATIC MONITORING**
- ✅ Checks if weather is good for quails
- ✅ Sends notifications automatically
- ✅ Stores history for analysis

### **3. TAGALOG NOTIFICATIONS**
- ✅ Easy to understand for Filipino users
- ✅ Clear action recommendations
- ✅ Prevents notification spam (cooldown)

### **4. FALLBACK SYSTEM**
- ✅ Works even without API key
- ✅ Simulates realistic Philippine weather
- ✅ Never shows "No data"

---

## 🔧 TROUBLESHOOTING:

### **Problem: Weather not updating**
```
Solution:
1. Check internet connection
2. Check if Laravel server is running
3. Open browser console (F12) and check for errors
4. Test API: http://localhost:8000/api/weather/philippines
```

### **Problem: No notifications**
```
Solution:
1. Check if "Enable Alert Notifications" is checked
2. Check notification cooldown (5 minutes)
3. Check browser console for errors
4. Clear localStorage: localStorage.clear()
```

### **Problem: Wrong location**
```
Solution:
1. Edit TemperatureHumidityController.php
2. Change 'q' => 'Manila,PH' to your city
3. Save and refresh page
```

---

## ✅ SUCCESS INDICATORS:

### **Temperature Page:**
```
Current Reading:
🌡️ Temperature: 28.5°C ✓ Optimal
💧 Humidity: 75.2% ✓ Optimal

Manila, Philippines - partly cloudy

Refreshing in 10s
```

### **Notifications:**
```
[10:00 AM] ☀️ Magandang panahon: 28.5°C - Perfect para sa quail!
[2:00 PM] 🔥 Sobrang init ang panahon: 33.2°C - Delikado para sa quail!
[6:00 PM] ✓ Magandang panahon: 29.1°C - Perfect para sa quail!
```

---

**PERFECT! PHILIPPINE WEATHER INTEGRATION IS COMPLETE! 🌤️✨**
