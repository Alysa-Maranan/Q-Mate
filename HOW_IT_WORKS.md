# 🎯 AUTOMATIC ANIMATION & NOTIFICATION GUIDE

## ✅ PAANO GUMAGANA:

### **AUTOMATIC BEHAVIOR:**

1. **Manual Feed:**
   - Click "Feed Now" button
   - Controller creates `animation_trigger.json` at `feeding_notification.json`
   - Servo motor starts moving
   - **Feeder page** - Polls every 1 second, detects file, shows animation
   - **Dashboard** - Polls every 5 seconds, detects file, shows notification
   - After feeding completes - Files deleted, animation disappears

2. **Scheduled Feed:**
   - Windows Task Scheduler checks every minute
   - Kapag match ang time - Auto-trigger
   - Controller creates files
   - Same behavior as manual feed

---

## 🔄 POLLING SYSTEM:

### **Feeder Page (Animation):**
```javascript
// Checks every 1 second
fetch('/api/feeder/animation')
  .then(data => {
    if (data.active) {
      // Show animation
      // Show "Feeding Time!" modal
      // After clicking "OK, Got it" - Run bowl animation
    }
  })
```

### **Dashboard (Notification):**
```javascript
// Checks every 5 seconds
fetch('/api/feeder/feeding-notification')
  .then(data => {
    if (data.active) {
      // Show modal notification
      // Auto-hide after 5 seconds
      // File deleted after reading
    }
  })
```

---

## 📋 STEP-BY-STEP FLOW:

### **SCENARIO 1: Manual Feed**

```
1. User clicks "Feed Now" (10 seconds, Cage 1)
   ↓
2. AJAX request to /feeder/manual/feed
   ↓
3. Controller creates:
   - storage/logs/animation_trigger.json
   - storage/logs/feeding_notification.json
   - storage/logs/servo_command.json
   ↓
4. Servo bridge reads servo_command.json
   ↓
5. Servo motor opens (10 seconds)
   ↓
6. FEEDER PAGE (if open):
   - Polls /api/feeder/animation
   - Detects active: true
   - Shows "Feeding Time!" modal
   - User clicks "OK, Got it"
   - Bowl animation starts
   - Progress bar moves
   - Countdown timer
   ↓
7. DASHBOARD (if open):
   - Polls /api/feeder/feeding-notification
   - Detects active: true
   - Shows modal notification
   - Auto-hides after 5 seconds
   - File deleted
   ↓
8. After 10 seconds:
   - Servo motor closes
   - animation_trigger.json deleted
   - Animation disappears
   - Feed history updated
```

### **SCENARIO 2: Scheduled Feed**

```
1. Windows Task Scheduler runs every minute
   ↓
2. Calls: php artisan feeder:auto-check
   ↓
3. Checks if current time matches any schedule
   ↓
4. If match found:
   - Same flow as Manual Feed
   - Creates same files
   - Triggers servo
   - Shows animation & notification
```

---

## 🎬 ANIMATION LIFECYCLE:

```
TIME    | ACTION
--------|--------------------------------------------------
0:00    | User clicks "Feed Now"
0:01    | Files created (animation_trigger.json)
0:02    | Feeder page detects file
0:02    | "Feeding Time!" modal appears
0:03    | User clicks "OK, Got it"
0:03    | Bowl animation starts
0:04    | Pellets falling, progress bar moving
0:05    | Countdown: 5 seconds remaining
0:10    | Feeding completes
0:10    | animation_trigger.json deleted
0:10    | Animation disappears
0:10    | Feed history updated
```

---

## 🔔 NOTIFICATION LIFECYCLE:

```
TIME    | ACTION
--------|--------------------------------------------------
0:00    | Feeding triggered
0:01    | feeding_notification.json created
0:02    | Dashboard detects file
0:02    | Modal notification appears
0:07    | Auto-hides after 5 seconds
0:07    | File deleted (one-time show)
```

---

## ✅ REQUIREMENTS:

### **For Animation to Show:**
- ✅ Feeder page must be OPEN
- ✅ animation_trigger.json must exist
- ✅ File must have `active: true`
- ✅ Polling script running (automatic)

### **For Notification to Show:**
- ✅ Dashboard must be OPEN
- ✅ feeding_notification.json must exist
- ✅ File must have `active: true`
- ✅ Polling script running (automatic)

---

## 🧪 HOW TO TEST:

### **Test 1: Manual Feed**
1. Open feeder page: http://localhost:8000/feeder
2. Open dashboard in another tab: http://localhost:8000/dashboard
3. Go back to feeder page
4. Scroll to "Manual Feed" section
5. Set duration: 10 seconds
6. Click "Feed Now"
7. **WAIT 1-2 SECONDS**
8. "Feeding Time!" modal should appear
9. Click "OK, Got it"
10. Bowl animation should start
11. Switch to dashboard tab
12. Modal notification should appear (if not shown yet)

### **Test 2: Scheduled Feed**
1. Open feeder page
2. Go to "Schedule" tab
3. Add schedule: 2 minutes from now, 10 seconds, Cage 1
4. Click "Add Schedule"
5. Keep feeder page open
6. Open dashboard in another tab
7. **WAIT for scheduled time**
8. Animation should auto-appear on feeder page
9. Notification should auto-appear on dashboard

---

## 🐛 TROUBLESHOOTING:

### **Animation not showing:**
```bash
# Check if file exists
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\animation_trigger.json

# Check contents
type c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\animation_trigger.json

# Should show:
# {"active":true,"duration":10,"cage":1,"started_at":"..."}
```

**Fix:**
- Make sure feeder page is open
- Check browser console (F12) for errors
- Refresh page (Ctrl+F5)

### **Notification not showing:**
```bash
# Check if file exists
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\feeding_notification.json

# Check contents
type c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\feeding_notification.json
```

**Fix:**
- Make sure dashboard is open BEFORE feeding starts
- File is deleted after first read (one-time notification)
- Refresh dashboard and trigger feeding again

### **Files not being created:**
```bash
# Check Laravel logs
type c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\laravel.log

# Check permissions
icacls c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs
```

---

## 📝 IMPORTANT NOTES:

1. **Animation only shows on FEEDER page**
2. **Notification only shows on DASHBOARD**
3. **Both pages must be OPEN to see them**
4. **Polling is AUTOMATIC - no manual trigger needed**
5. **Files are AUTO-DELETED after feeding completes**
6. **Notification shows ONCE per feeding (file deleted after read)**
7. **Animation shows CONTINUOUSLY until feeding completes**

---

## 🎯 EXPECTED BEHAVIOR:

### **✅ CORRECT:**
- Click "Feed Now" → Wait 1-2 seconds → Animation appears
- Scheduled time arrives → Animation appears automatically
- Dashboard open → Notification appears when feeding starts
- Feeding completes → Animation disappears

### **❌ INCORRECT:**
- Click "Feed Now" → Animation appears IMMEDIATELY (too fast)
- Animation shows even when NOT feeding
- Notification shows multiple times for same feeding
- Animation doesn't disappear after feeding completes

---

## 🚀 QUICK TEST:

```bash
# 1. Start servo bridge
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
start_servo_bridge.bat

# 2. Start Laravel server
cd ..
php artisan serve

# 3. Open pages
start http://localhost:8000/feeder
start http://localhost:8000/dashboard

# 4. In feeder page:
#    - Scroll to "Manual Feed"
#    - Click "Feed Now"
#    - WAIT 1-2 seconds
#    - "Feeding Time!" modal should appear
#    - Click "OK, Got it"
#    - Bowl animation should start

# 5. Switch to dashboard:
#    - Modal notification should appear
```

---

**EVERYTHING IS AUTOMATIC! JUST TRIGGER FEEDING AND WAIT! ⏱️✨**
