# 🎯 SIMPLE GUIDE - MANUAL FEEDING

## ✅ NASAAN ANG MANUAL FEED?

### **LOCATION:**
```
http://localhost:8000/feeder
```

1. Open feeder page
2. Make sure "Feeding Info" tab is active (first tab)
3. **SCROLL DOWN** - Manual Feed section is BELOW the timeline
4. You'll see:
   - Duration (seconds) input
   - Cage Number input
   - "▶ Feed Now" button

---

## 🔧 KUNG WALA PANG MANUAL FEED SECTION:

### **OPTION 1: Refresh Page**
```
Press Ctrl + F5 (hard refresh)
```

### **OPTION 2: Clear Cache**
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### **OPTION 3: Restart Server**
```bash
# Press Ctrl+C to stop server
php artisan serve
```

---

## 🗑️ KUNG MAY ANIMATION PA RIN (HINDI NAWAWALA):

### **STEP 1: Delete Old Files**
```bash
cd c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs
del animation_trigger.json
del feeding_notification.json
```

### **STEP 2: Refresh Browser**
```
Press Ctrl + F5
```

### **STEP 3: Restart Servo Bridge**
```bash
taskkill /F /IM pythonw.exe
cd c:\xampp\htdocs\CAPSTONE SQUIFM\scripts
start_servo_bridge.bat
```

---

## 📋 COMPLETE RESET (IF NOTHING WORKS):

```bash
# 1. Stop everything
taskkill /F /IM pythonw.exe
taskkill /F /IM php.exe

# 2. Delete old files
cd c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs
del *.json

# 3. Clear cache
cd c:\xampp\htdocs\CAPSTONE SQUIFM
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 4. Start servo bridge
cd scripts
start_servo_bridge.bat

# 5. Start Laravel server
cd ..
php artisan serve

# 6. Open browser (NEW TAB)
start http://localhost:8000/feeder
```

---

## 🎬 PAANO GAMITIN ANG MANUAL FEED:

### **STEP 1: Open Feeder Page**
```
http://localhost:8000/feeder
```

### **STEP 2: Scroll Down**
Look for "Manual Feed" section (below the timeline)

### **STEP 3: Fill Form**
- Duration: 10 seconds
- Cage Number: 1

### **STEP 4: Click "Feed Now"**
- Wait 3-5 seconds
- "Feeding Time!" modal should appear
- Click "OK, Got it"
- Bowl animation starts

---

## ✅ CHECKLIST:

- [ ] Laravel server running (`php artisan serve`)
- [ ] Servo bridge running (`start_servo_bridge.bat`)
- [ ] Feeder page open
- [ ] "Feeding Info" tab active
- [ ] Scrolled down to see Manual Feed section
- [ ] No old animation files (`del *.json`)
- [ ] Browser cache cleared (Ctrl+F5)

---

## 🔍 KUNG WALA PA RIN:

### **Take Screenshot:**
1. Full feeder page (scroll down)
2. Browser console (F12)
3. Send to me

### **Check Files:**
```bash
dir c:\xampp\htdocs\CAPSTONE SQUIFM\storage\logs\*.json
```

### **Check Processes:**
```bash
tasklist | findstr pythonw
tasklist | findstr php
```

---

## 🚀 QUICK START:

```bash
# Run this ONE command:
cd c:\xampp\htdocs\CAPSTONE SQUIFM && taskkill /F /IM pythonw.exe 2>nul & taskkill /F /IM php.exe 2>nul & del storage\logs\*.json 2>nul & php artisan cache:clear & cd scripts & start_servo_bridge.bat & cd .. & start /B php artisan serve & timeout /t 3 /nobreak >nul & start http://localhost:8000/feeder
```

**TAPOS NA! REFRESH LANG ANG PAGE! 🔄**
