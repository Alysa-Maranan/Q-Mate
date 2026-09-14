# FEEDER SYSTEM - QUICK REFERENCE

## 🚀 QUICK START (3 STEPS!)

```
1. Double-click: start_feeder_system.bat
2. Open browser: Feeder Management
3. Use feeder - Automatic na lahat!
```

## 📋 COMMANDS

| Command | Purpose |
|---------|---------|
| `start_feeder_system.bat` | Start everything |
| `stop_feeder_system.bat` | Stop everything |
| `check_servo_bridge.bat` | Check if running |

## ✅ WHAT'S FIXED

| Before | After |
|--------|-------|
| ❌ Need to refresh to hide animation | ✅ Auto-hide animation |
| ❌ Servo doesn't work on schedule | ✅ Servo works on schedule |
| ❌ Manual process | ✅ Automatic process |

## 🔧 TROUBLESHOOTING

### Animation not appearing?
```bash
check_servo_bridge.bat
# If not running:
start_feeder_system.bat
```

### Animation not disappearing?
```bash
# Wait 5 seconds, then:
F5 (refresh)
# Or delete:
del storage\logs\animation_trigger.json
```

### Servo not moving?
```bash
# 1. Check USB connection
# 2. Check COM port in Device Manager
# 3. Restart system:
stop_feeder_system.bat
start_feeder_system.bat
```

### Schedule not triggering?
```bash
# 1. Check servo bridge is running
# 2. Check scheduler is running
# 3. Check time matches exactly
# 4. Check logs:
type storage\logs\laravel.log
```

## 📁 IMPORTANT FILES

| File | Purpose |
|------|---------|
| `servo_command.json` | Command queue |
| `animation_trigger.json` | Animation state |
| `feeding_notification.json` | Dashboard notification |
| `servo_result.json` | Execution result |

## 🎯 TESTING CHECKLIST

### Manual Feed Test
- [ ] Start system
- [ ] Click "Manual Feed"
- [ ] Select cage & duration
- [ ] Click "Feed Now"
- [ ] Animation appears
- [ ] Servo moves
- [ ] Animation disappears
- [ ] Record in history

### Schedule Test
- [ ] Start system
- [ ] Set schedule 1 min ahead
- [ ] Wait for time
- [ ] Animation appears automatically
- [ ] Servo moves
- [ ] Animation disappears
- [ ] Record in history

## 💡 PRO TIPS

1. **Always start servo bridge first!**
   ```bash
   start_feeder_system.bat
   ```

2. **Check if running:**
   ```bash
   check_servo_bridge.bat
   ```

3. **Auto-start on boot:**
   - Create shortcut of `start_feeder_system.bat`
   - Win+R → `shell:startup`
   - Copy shortcut there

4. **Debug mode:**
   - Check `storage\logs\laravel.log`
   - Check browser console (F12)
   - Check servo bridge output

## 🔄 ANIMATION FLOW

```
User Action
    ↓
Laravel writes command
    ↓
Servo bridge detects
    ↓
Creates animation file
    ↓
Browser shows animation
    ↓
Servo executes
    ↓
Deletes animation file
    ↓
Browser hides animation
    ↓
Page reloads
```

## ⚠️ IMPORTANT NOTES

- **Servo bridge must be running** before using feeder
- **Animation is automatic** - no refresh needed
- **Works for both** manual and scheduled feeding
- **Check logs** if something goes wrong
- **Restart system** if stuck

## 📞 SUPPORT

If problems persist:
1. Check logs: `storage\logs\laravel.log`
2. Check servo bridge output
3. Check browser console (F12)
4. Restart: `stop_feeder_system.bat` → `start_feeder_system.bat`

## 🎉 SUCCESS INDICATORS

✅ Servo bridge running (pythonw.exe in Task Manager)
✅ Animation appears on feed
✅ Animation disappears automatically
✅ Servo moves on schedule
✅ Records in feed history

---

**Remember**: `start_feeder_system.bat` → Use feeder → Automatic! 🚀
