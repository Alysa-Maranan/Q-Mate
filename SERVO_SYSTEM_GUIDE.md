# SERVO SYSTEM - PURE BACKGROUND SERVICE

## PAANO GUMAGANA (NO TERMINAL NEEDED)

### 1. BACKGROUND SERVICE
```
pythonw scripts\servo_bridge.py
```
- Tumatakbo sa background (walang terminal window)
- Nag-watch ng `storage/logs/servo_command.json`
- Automatic na nag-control ng servo
- Lahat ng logs ay sa `storage/logs/servo_bridge.log`

### 2. SYSTEM FLOW

#### PAG MAY FEEDING (Manual o Scheduled):
```
Laravel Controller
    ↓
Creates servo_command.json
    ↓
servo_bridge.py detects file
    ↓
OPENS SERVO + Creates animation_trigger.json
    ↓
Browser detects animation_trigger.json (every 300ms)
    ↓
SHOWS DISPENSING ANIMATION
    ↓
After duration seconds...
    ↓
CLOSES SERVO + Deletes animation_trigger.json
    ↓
Browser hides animation
    ↓
Shows "Done Feeding" modal
```

### 3. KEY FILES

**servo_command.json** (Trigger file)
```json
{
  "action": "trigger",
  "duration": 10,
  "cage": 1,
  "port": "COM4",
  "type": "manual"
}
```

**animation_trigger.json** (Status file)
```json
{
  "active": true,
  "duration": 10,
  "cage": 1,
  "started_at": "2025-01-15T14:30:00+08:00"
}
```

**servo_result.json** (Result file)
```json
{
  "success": true,
  "port": "COM4",
  "duration": 10,
  "timestamp": 1705302600
}
```

### 4. BROWSER CHECKING

Frontend checks `/api/feeder/animation` every **300ms** (0.3 seconds):
- Sobrang bilis ng detection
- Instant animation pag nag-start ang servo
- Instant hide pag nag-close ang servo

### 5. TIMING GUARANTEE

✅ **SERVO OPENS** = **ANIMATION STARTS**
- servo_bridge.py creates animation_trigger.json SAME TIME as servo opens
- Browser detects within 300ms

✅ **SERVO CLOSES** = **ANIMATION ENDS**
- servo_bridge.py deletes animation_trigger.json SAME TIME as servo closes
- Browser hides animation within 300ms

### 6. NO TERMINAL DEPENDENCY

❌ **HINDI NA KAILANGAN:**
- Manual terminal commands
- Watching terminal output
- Checking console logs

✅ **PURE SYSTEM-BASED:**
- Background service
- File-based communication
- Automatic operation

### 7. PAANO I-START

**One-time setup:**
```bash
# Start servo bridge as background service
pythonw scripts\servo_bridge.py
```

**Check if running:**
```bash
# Check log file
type storage\logs\servo_bridge.log
```

**Stop service:**
```bash
# Kill pythonw process
taskkill /F /IM pythonw.exe
```

### 8. TROUBLESHOOTING

**Walang animation?**
- Check if servo_bridge.py is running
- Check servo_bridge.log for errors
- Check if animation_trigger.json exists during feeding

**Late ang animation?**
- Should be instant (300ms detection)
- Check browser console for errors
- Check network tab for /api/feeder/animation calls

**Servo not working?**
- Check servo_bridge.log
- Check COM port (default: COM4)
- Check USB connection to WEMOS

## SUMMARY

Ang sistema ay **PURE BACKGROUND** na:
1. servo_bridge.py = Background service (no terminal)
2. servo_command.json = Trigger file (Laravel creates)
3. animation_trigger.json = Status file (servo_bridge creates/deletes)
4. Browser = Checks every 300ms, shows/hides animation automatically

**WALANG TERMINAL DEPENDENCY - LAHAT AUTOMATIC!** ✅
