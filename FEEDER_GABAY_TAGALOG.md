# Feeder System - Gabay sa Paggamit

## Mga Naayos na Problema

### 1. Animation Hindi Nawawala Automatically
**Problema**: Nananatili ang animation sa screen kahit tapos na ang feeding time - kailangan pa i-refresh.

**Solusyon**: Na-update na ang animation system para automatic na mawala kapag tapos na ang feeding.

### 2. Servomotor Hindi Gumagana sa Schedule
**Problema**: Kapag nag-set ng schedule, hindi umaandar ang servomotor.

**Solusyon**: Kailangan running ang servo_bridge.py bago mag-set ng schedule.

## Paano Gamitin

### Step 1: I-start ang Servo Bridge (KAILANGAN!)
```bash
cd scripts
start_servo_bridge.bat
```

O double-click lang ang `start_servo_bridge.bat` sa scripts folder.

### Step 2: I-check kung Running
1. Buksan ang Task Manager (Ctrl+Shift+Esc)
2. Hanapin ang "pythonw.exe" sa Processes
3. Kung nandoon, running na ang servo bridge

### Step 3: Gamitin ang Feeder
1. Buksan ang Feeder Management page
2. Mag-set ng schedule o manual feed
3. Lalabas ang animation automatically
4. Mawawala ang animation automatically kapag tapos na

## Troubleshooting

### Hindi Lumalabas ang Animation
- I-check kung running ang servo_bridge.py
- I-restart ang servo_bridge.bat
- I-check ang browser console (F12)

### Hindi Nawawala ang Animation
- I-refresh ang page (F5)
- I-delete manually ang `storage/logs/animation_trigger.json`
- I-restart ang servo_bridge.py

### Hindi Umaandar ang Servo
1. I-check kung connected ang WEMOS sa USB
2. I-check ang COM port (default: COM4)
3. I-check kung running ang servo_bridge.py
4. I-restart ang servo_bridge.bat

### Hindi Nag-trigger ang Schedule
1. I-check kung running ang servo_bridge.py
2. I-check kung running ang scheduler: `run_scheduler.bat`
3. I-check ang time - dapat exact match
4. I-check ang Laravel logs

## Importante!

- **Laging i-start ang servo_bridge.py bago gumamit ng feeder**
- Automatic na ang animation - hindi na kailangan mag-refresh
- Gumana ang animation para sa manual at scheduled feeding
- Kung stuck ang animation, i-delete ang `animation_trigger.json`

## Auto-Start (Optional)

Para automatic na mag-start ang servo bridge kapag nag-boot:

1. Gumawa ng shortcut ng `start_servo_bridge.bat`
2. Press Win+R, type `shell:startup`, press Enter
3. I-copy ang shortcut sa Startup folder
4. Automatic na mag-start kapag nag-login

## Testing

### Test Manual Feed
1. Pumunta sa Feeder Management
2. Click "Manual Feed"
3. Piliin ang duration at cage
4. Click "Feed Now"
5. Dapat lumabas ang animation
6. Dapat mawala ang animation after feeding

### Test Schedule
1. Mag-set ng schedule 1 minute from now
2. Hintayin ang scheduled time
3. Dapat lumabas ang animation automatically
4. Dapat mawala ang animation after feeding
5. I-check ang Feed History

## Mga File na Involved

- `servo_command.json` - Command para sa servo
- `animation_trigger.json` - Animation state (ginagawa/dine-delete ng servo_bridge.py)
- `feeding_notification.json` - Notification para sa dashboard
- `servo_result.json` - Result ng servo execution

## Mga Command

### I-start ang Servo Bridge
```bash
scripts\start_servo_bridge.bat
```

### I-check kung Running
```bash
check_servo_bridge.bat
```

### I-stop ang Servo Bridge
1. Buksan ang Task Manager
2. Hanapin ang "pythonw.exe"
3. Right-click → End Task

## Kailangan Tandaan

- Servo bridge ay tumatakbo sa background
- Hindi mo makikita ang window nito (pythonw = windowless)
- I-check sa Task Manager kung running
- Kung may problema, i-restart lang ang servo_bridge.bat
- I-check ang `storage/logs/` para sa debug files
