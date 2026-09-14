# 🐦 AUTOMATIC FEEDER SETUP GUIDE

## ✅ FIXES IMPLEMENTED:

### 1. ✅ Servo gumagalaw na sa set time
- Added automatic scheduler na tumatakbo every minute
- Nag-trigger ng servo kapag match ang time sa schedule

### 2. ✅ May dispensing animation na
- Animation trigger file created when feeding starts
- Browser polls every second para makita ang animation
- Bowl animation with falling pellets

### 3. ✅ Dashboard notification modal
- Modal notification sa dashboard kapag nagsimula ang feeding
- Shows feeding type (Manual/Scheduled), cage number, at duration
- Auto-disappears after 5 seconds
- Notification also saved sa dashboard notifications list

---

## 📋 SETUP INSTRUCTIONS:

### STEP 1: I-check ang Requirements
```bash
# Check if Python is installed
python --version

# Check if PySerial is installed
pip list | findstr pyserial

# If not installed:
pip install pyserial
```

### STEP 2: I-connect ang Servo Motor
1. Plug servo motor sa USB port
2. Check sa Device Manager kung anong COM port (dapat COM4)
3. Kung hindi COM4, i-update ang `.env` file:
   ```
   SERVO_SERIAL_PORT=COM3  # or whatever port
   ```

### STEP 3: I-start ang Servo Bridge
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
start_servo_bridge.bat
```

### STEP 4: I-setup ang Automatic Feeder
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM
setup_auto_feeder.bat
```

Ito ang gagawin nito:
- Start servo bridge (background process)
- Create Windows Task Scheduler task
- Check schedules every minute
- Trigger feeding automatically

### STEP 5: I-start ang Laravel Server
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM
php artisan serve
```

---

## 🧪 TESTING:

### TEST 1: Manual Feed (Immediate)
1. Go to: http://localhost:8000/feeder
2. Click "Feeding Info" tab
3. Scroll down to "Manual Feed" section
4. Set duration (e.g., 5 seconds)
5. Select cage number
6. Click "Feed Now"

**Expected Results:**
- ✅ Servo motor gumagalaw
- ✅ Dispensing animation sa browser
- ✅ Dashboard notification modal (if dashboard is open)
- ✅ Feed history updated

### TEST 2: Scheduled Feed
1. Go to: http://localhost:8000/feeder
2. Click "Schedule" tab
3. Add new schedule:
   - Date & Time: Set to 1-2 minutes from now
   - Duration: 10 seconds
   - Cage: 1
4. Click "Add Schedule"
5. Wait for the scheduled time

**Expected Results:**
- ✅ Servo motor gumagalaw sa exact time
- ✅ Dispensing animation sa browser
- ✅ Dashboard notification modal
- ✅ Feed history updated
- ✅ Schedule status changes to "Done"

### TEST 3: Dashboard Notification
1. Open dashboard: http://localhost:8000/dashboard
2. Open feeder page sa another tab: http://localhost:8000/feeder
3. Trigger manual feed sa feeder page
4. Switch back to dashboard tab

**Expected Results:**
- ✅ Modal notification appears
- ✅ Shows feeding type, cage, duration
- ✅ Notification added to list
- ✅ Auto-disappears after 5 seconds

---

## 🔧 TROUBLESHOOTING:

### Problem: Servo hindi gumagalaw
**Solution:**
1. Check if servo bridge is running:
   ```bash
   tasklist | findstr pythonw
   ```
2. If not running, start it:
   ```bash
   cd scripts
   start_servo_bridge.bat
   ```
3. Check COM port sa Device Manager
4. Check if servo_command.json is created:
   ```bash
   dir storage\logs\servo_command.json
   ```

### Problem: Walang animation
**Solution:**
1. Check if animation_trigger.json exists:
   ```bash
   dir storage\logs\animation_trigger.json
   ```
2. Open browser console (F12) and check for errors
3. Refresh the page

### Problem: Walang dashboard notification
**Solution:**
1. Check if feeding_notification.json is created:
   ```bash
   dir storage\logs\feeding_notification.json
   ```
2. Make sure dashboard is open when feeding starts
3. Check browser console for errors

### Problem: Schedule hindi nag-trigger
**Solution:**
1. Check if Task Scheduler task is running:
   ```bash
   schtasks /query /tn "QuailFeederAutoCheck"
   ```
2. If not found, run setup again:
   ```bash
   setup_auto_feeder.bat
   ```
3. Check Laravel logs:
   ```bash
   type storage\logs\laravel.log
   ```

---

## 🛑 STOPPING THE FEEDER:

To stop automatic feeding:
```bash
stop_auto_feeder.bat
```

This will:
- Stop the Windows Task Scheduler task
- Stop the servo bridge
- Disable automatic feeding

---

## 📝 IMPORTANT NOTES:

1. **Servo Bridge** - Dapat laging running para gumana ang servo
2. **Laravel Server** - Dapat running para gumana ang web interface
3. **Task Scheduler** - Automatic na mag-check every minute
4. **COM Port** - Make sure tama ang port sa .env file
5. **Python** - Make sure installed with pyserial

---

## 🎯 QUICK START (All-in-One):

```bash
# 1. Start servo bridge
cd scripts
start_servo_bridge.bat

# 2. Setup automatic feeder
cd ..
setup_auto_feeder.bat

# 3. Start Laravel server
php artisan serve

# 4. Open browser
start http://localhost:8000/feeder
```

---

## ✨ FEATURES:

### Feeding Info Tab
- Next feeding countdown
- Daily feeding schedule timeline
- Latest feed history
- Feed brand toggle

### Schedule Tab
- Add new schedules
- View all schedules
- Trigger now button
- Delete schedules

### Feed History Tab
- Complete feeding history
- Filter by date
- Delete records
- Export data

### Dashboard
- Real-time notifications
- Feeding status
- System overview
- Activity log

---

## 📞 SUPPORT:

If may problema pa rin, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Servo bridge logs: `storage/logs/servo_result.json`
3. Browser console (F12)
4. Windows Event Viewer

---

**ENJOY YOUR AUTOMATIC QUAIL FEEDER! 🐦🍽️**
