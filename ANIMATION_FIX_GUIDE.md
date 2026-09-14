# 🔧 ANIMATION & NOTIFICATION FIX GUIDE

## ✅ GINAWA KO NA:

1. ✅ Added Manual Feed form sa Feeding Info tab
2. ✅ Added AJAX handler para sa manual feed
3. ✅ Added animation polling script
4. ✅ Added dashboard notification polling
5. ✅ Created test files (animation_trigger.json, feeding_notification.json)
6. ✅ Created test page (test-feeder-files.blade.php)

---

## 🧪 STEP-BY-STEP TESTING:

### STEP 1: Check if files are being created

1. Open browser: http://localhost:8000/test-feeder-files
2. Makikita mo kung may laman ang animation at notification files
3. Dapat "active: false" kung walang feeding

### STEP 2: Test Manual Feed

1. Go to: http://localhost:8000/feeder
2. Scroll down to "Manual Feed" section
3. Set duration: 10 seconds
4. Set cage: 1
5. Click "Feed Now"
6. **EXPECTED:**
   - Success notification sa top
   - Animation overlay appears (bowl with falling pellets)
   - Progress bar moving
   - Countdown timer

### STEP 3: Check Browser Console

1. Press F12 to open Developer Tools
2. Go to Console tab
3. Click "Feed Now" again
4. **LOOK FOR:**
   - "Animation check: {active: true, ...}"
   - Any error messages

### STEP 4: Check if files are created

```bash
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\*.json
```

**EXPECTED FILES:**
- animation_trigger.json
- feeding_notification.json
- servo_result.json

### STEP 5: Test Dashboard Notification

1. Open dashboard: http://localhost:8000/dashboard
2. Open feeder in another tab: http://localhost:8000/feeder
3. Click "Feed Now" sa feeder tab
4. Switch back to dashboard tab
5. **EXPECTED:**
   - Modal notification appears
   - Shows "Feeding Started!"
   - Shows cage number and duration

---

## 🐛 TROUBLESHOOTING:

### Problem: Walang animation
**Check:**
```bash
# 1. Check if animation_trigger.json exists
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\animation_trigger.json

# 2. Check contents
type c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\animation_trigger.json
```

**Fix:**
- Make sure servo bridge is running
- Check browser console for errors
- Try refreshing the page

### Problem: Walang dashboard notification
**Check:**
```bash
# 1. Check if feeding_notification.json exists
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\feeding_notification.json

# 2. Check contents
type c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\feeding_notification.json
```

**Fix:**
- Make sure dashboard is open when feeding starts
- Check browser console for errors
- Try refreshing dashboard

### Problem: Manual feed button walang effect
**Check:**
1. Open browser console (F12)
2. Click "Feed Now"
3. Look for error messages

**Common Issues:**
- CSRF token mismatch - Refresh page
- Route not found - Check routes/feeder.php
- Controller error - Check storage/logs/laravel.log

---

## 📝 MANUAL TEST (If automatic doesn't work):

### Create animation file manually:
```bash
echo {"active":true,"duration":10,"cage":1,"started_at":"2026-04-21T05:04:00+08:00"} > c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\animation_trigger.json
```

### Create notification file manually:
```bash
echo {"active":true,"type":"manual","duration":10,"cage":1,"started_at":"2026-04-21T05:04:00+08:00"} > c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\feeding_notification.json
```

### Then:
1. Refresh feeder page - animation should appear
2. Refresh dashboard - notification should appear

---

## 🔍 DEBUG CHECKLIST:

- [ ] Laravel server running (php artisan serve)
- [ ] Servo bridge running (scripts\start_servo_bridge.bat)
- [ ] Browser console open (F12)
- [ ] No JavaScript errors in console
- [ ] CSRF token valid (refresh page if needed)
- [ ] Routes exist (check routes/feeder.php)
- [ ] Controller methods exist (check FeedingScheduleController.php)
- [ ] Storage/logs folder writable
- [ ] API routes accessible (/api/feeder/animation, /api/feeder/feeding-notification)

---

## 🎯 QUICK FIX:

If nothing works, try this:

```bash
# 1. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 2. Restart Laravel server
# Press Ctrl+C to stop
php artisan serve

# 3. Restart servo bridge
cd scripts
start_servo_bridge.bat

# 4. Refresh browser (Ctrl+F5)
```

---

## 📞 NEXT STEPS:

1. Run test page: http://localhost:8000/test-feeder-files
2. Check browser console for errors
3. Try manual feed
4. Check if files are created
5. Report back what you see!

**I-screenshot mo ang:**
- Browser console (F12)
- Test page results
- Any error messages
