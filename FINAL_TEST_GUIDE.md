# 🎯 FINAL TEST GUIDE - ANIMATION & NOTIFICATION

## ✅ LAHAT NG KAILANGAN MO:

### FILES CREATED:
1. ✅ Manual Feed form (sa feeder page)
2. ✅ Animation polling script
3. ✅ Dashboard notification polling
4. ✅ Test page (test-feeding.blade.php)
5. ✅ Batch file (open_test_pages.bat)

---

## 🚀 EASIEST WAY TO TEST:

### **ONE-CLICK TEST:**

```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM
open_test_pages.bat
```

Ito ang gagawin nito:
1. ✅ Start Laravel server (if not running)
2. ✅ Open test page
3. ✅ Open feeder page
4. ✅ Open dashboard

---

## 📋 STEP-BY-STEP INSTRUCTIONS:

### **STEP 1: Open Test Page**
```
http://localhost:8000/test-feeding
```

### **STEP 2: Click "Start Feeding" Button**
- Green button na may 🍽️ icon
- Mag-trigger ng 10-second feeding

### **STEP 3: Check Feeder Page**
```
http://localhost:8000/feeder
```
**EXPECTED:**
- ✅ Bowl animation appears
- ✅ Falling pellets
- ✅ Progress bar moving
- ✅ Countdown timer
- ✅ "Dispensing Feed" text

### **STEP 4: Check Dashboard**
```
http://localhost:8000/dashboard
```
**EXPECTED:**
- ✅ Modal notification appears
- ✅ Shows "Feeding Started!"
- ✅ Shows cage number and duration
- ✅ Auto-disappears after 5 seconds

---

## 🔍 WHAT TO LOOK FOR:

### **On Test Page:**
```json
{
  "status": "feeding_now",
  "mode": "serial",
  "duration": 10,
  "cage_number": 1,
  "message": "Manual feeding started for Cage 1."
}
```

### **Animation File Status:**
```json
{
  "active": true,
  "duration": 10,
  "cage": 1,
  "started_at": "2026-04-21T..."
}
```

### **Notification File Status:**
```json
{
  "active": true,
  "type": "manual",
  "duration": 10,
  "cage": 1,
  "started_at": "2026-04-21T..."
}
```

---

## 🐛 IF WALANG LUMALABAS:

### **Check 1: Servo Bridge Running?**
```bash
tasklist | findstr pythonw
```
If walang result:
```bash
cd scripts
start_servo_bridge.bat
```

### **Check 2: Files Created?**
```bash
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\*.json
```
Should see:
- animation_trigger.json
- feeding_notification.json
- servo_result.json

### **Check 3: Browser Console**
- Press F12
- Go to Console tab
- Look for errors

### **Check 4: Laravel Logs**
```bash
type c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\laravel.log
```

---

## 🔧 MANUAL FIX (If automatic fails):

### **Create Files Manually:**

```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs

echo {"active":true,"duration":10,"cage":1,"started_at":"2026-04-21T05:04:00+08:00"} > animation_trigger.json

echo {"active":true,"type":"manual","duration":10,"cage":1,"started_at":"2026-04-21T05:04:00+08:00"} > feeding_notification.json
```

### **Then:**
1. Refresh feeder page - Animation should appear
2. Refresh dashboard - Notification should appear

---

## ✅ SUCCESS CHECKLIST:

- [ ] Test page opens
- [ ] "Start Feeding" button works
- [ ] Status shows "feeding_now"
- [ ] Animation file shows "active: true"
- [ ] Notification file shows "active: true"
- [ ] Feeder page shows bowl animation
- [ ] Dashboard shows modal notification
- [ ] Servo motor moves (if connected)

---

## 📞 TROUBLESHOOTING COMMANDS:

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Restart server
# Press Ctrl+C then:
php artisan serve

# Check routes
php artisan route:list | findstr feeder

# Check permissions
icacls c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs
```

---

## 🎯 EXPECTED BEHAVIOR:

### **Timeline:**
1. **0s** - Click "Start Feeding"
2. **0.5s** - Files created (animation_trigger.json, feeding_notification.json)
3. **1s** - Feeder page detects animation file
4. **1s** - Dashboard detects notification file
5. **1s** - Animation overlay appears on feeder page
6. **1s** - Modal notification appears on dashboard
7. **10s** - Feeding completes
8. **10s** - Animation disappears
9. **10s** - Files deleted
10. **10s** - Feed history updated

---

## 📸 SCREENSHOT CHECKLIST:

Kung hindi gumagana, i-screenshot:
1. Test page result
2. Browser console (F12)
3. Feeder page
4. Dashboard
5. File listing (dir storage\logs\*.json)

---

## 🚨 COMMON ISSUES:

### Issue: "CSRF token mismatch"
**Fix:** Refresh page (Ctrl+F5)

### Issue: "Route not found"
**Fix:** 
```bash
php artisan route:clear
php artisan cache:clear
```

### Issue: "Permission denied"
**Fix:**
```bash
icacls c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs /grant Everyone:F /T
```

### Issue: Animation not showing
**Fix:** Check browser console for JavaScript errors

### Issue: Notification not showing
**Fix:** Make sure dashboard is open BEFORE triggering feeding

---

## 🎉 FINAL NOTES:

- Animation appears ONLY on feeder page
- Notification appears ONLY on dashboard
- Both need to be open to see them
- Files are auto-deleted after feeding completes
- Test page shows real-time status

**RUN THIS NOW:**
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM
open_test_pages.bat
```

**THEN CLICK "START FEEDING" AND WATCH THE MAGIC! ✨**
