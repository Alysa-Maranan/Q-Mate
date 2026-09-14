# Feeder System Fix Guide

## Issues Fixed

### 1. Animation Not Disappearing Automatically
**Problem**: Animation stays on screen even after feeding time arrives - requires manual refresh.

**Solution**: Updated the animation polling system to properly detect when the servo closes and automatically hide the animation + reload the page.

**Changes Made**:
- Modified `runAnimation()` function to add a 1-second delay before hiding animation and reloading
- Fixed `checkOnce()` to properly handle animation state checking
- Animation now automatically disappears when `animation_trigger.json` is deleted by servo_bridge.py

### 2. Servomotor Not Working on Schedule
**Problem**: When setting a schedule in the system, the servomotor doesn't activate.

**Root Cause**: The servo_bridge.py script needs to be running in the background to watch for commands.

**Solution**: 
1. Ensure servo_bridge.py is running before setting schedules
2. The script watches `storage/logs/servo_command.json` and executes commands
3. Creates animation file for both manual and scheduled feeding

## How to Use

### Step 1: Start Servo Bridge (REQUIRED)
```bash
cd scripts
start_servo_bridge.bat
```

This starts the servo bridge in the background. It will:
- Watch for feeding commands
- Control the servo motor
- Create animation triggers
- Delete animation when servo closes

### Step 2: Verify Servo Bridge is Running
Check if `pythonw.exe` process is running in Task Manager.

### Step 3: Set Schedule or Manual Feed
- Go to Feeder Management page
- Set a schedule or trigger manual feed
- Animation will appear automatically
- Animation will disappear automatically when feeding completes

## Technical Details

### Animation Flow
1. Schedule/Manual feed triggers → writes `servo_command.json`
2. servo_bridge.py detects command → creates `animation_trigger.json`
3. Frontend polls `/api/feeder/animation` every 500ms
4. When servo closes → servo_bridge.py deletes `animation_trigger.json`
5. Frontend detects deletion → hides animation → reloads page

### Files Involved
- `servo_command.json` - Command queue for servo
- `animation_trigger.json` - Animation state (created/deleted by servo_bridge.py)
- `feeding_notification.json` - Dashboard notification
- `servo_result.json` - Result of servo execution

## Troubleshooting

### Animation Doesn't Appear
- Check if servo_bridge.py is running
- Check if `animation_trigger.json` exists in `storage/logs/`
- Check browser console for errors

### Animation Doesn't Disappear
- Check if servo_bridge.py successfully deletes `animation_trigger.json`
- Check if frontend polling is working (Network tab in browser)
- Manually delete `animation_trigger.json` if stuck

### Servo Doesn't Move
1. Check if servo_bridge.py is running
2. Check COM port (default: COM4)
3. Check if WEMOS is connected via USB
4. Check `storage/logs/servo_result.json` for errors

### Schedule Doesn't Trigger
1. Ensure servo_bridge.py is running
2. Check if auto-check is running: `run_scheduler.bat`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify schedule time matches current time

## Auto-Start Setup (Optional)

To automatically start servo_bridge.py on system boot:

1. Create a shortcut to `start_servo_bridge.bat`
2. Press Win+R, type `shell:startup`, press Enter
3. Copy the shortcut to the Startup folder
4. Servo bridge will start automatically on login

## Testing

### Test Animation
1. Go to Feeder Management
2. Click "Manual Feed"
3. Select duration and cage
4. Click "Feed Now"
5. Animation should appear immediately
6. Animation should disappear after feeding completes

### Test Schedule
1. Set a schedule 1 minute in the future
2. Wait for the scheduled time
3. Animation should appear automatically
4. Animation should disappear after feeding completes
5. Check Feed History for the record

## Important Notes

- **Always start servo_bridge.py before using the feeder system**
- Animation works for both manual and scheduled feeding
- Animation automatically disappears - no refresh needed
- If animation gets stuck, manually delete `animation_trigger.json`
- Check `storage/logs/` for debug files
