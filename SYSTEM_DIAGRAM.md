# FEEDER SYSTEM - VISUAL DIAGRAM

## SYSTEM ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                        │
│                    (Browser - Feeder Page)                   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ HTTP Request
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                      LARAVEL BACKEND                         │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  FeedingScheduleController                           │  │
│  │  - manualFeed()                                      │  │
│  │  - autoCheckSchedules()                              │  │
│  │  - store()                                           │  │
│  └──────────────────────┬───────────────────────────────┘  │
│                         │                                    │
│                         │ Writes                             │
│                         ▼                                    │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  storage/logs/servo_command.json                     │  │
│  │  {                                                   │  │
│  │    "action": "trigger",                              │  │
│  │    "duration": 10,                                   │  │
│  │    "cage": 1,                                        │  │
│  │    "type": "manual"                                  │  │
│  │  }                                                   │  │
│  └──────────────────────┬───────────────────────────────┘  │
└─────────────────────────┼────────────────────────────────────┘
                          │
                          │ Watches (polling every 1s)
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    SERVO BRIDGE (Python)                     │
│                    scripts/servo_bridge.py                   │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  1. Detects servo_command.json                       │  │
│  │  2. Reads command                                    │  │
│  │  3. Creates animation_trigger.json                   │  │
│  │  4. Sends command to WEMOS via Serial                │  │
│  │  5. Waits for duration                               │  │
│  │  6. Deletes animation_trigger.json                   │  │
│  │  7. Writes servo_result.json                         │  │
│  └──────────────────────┬───────────────────────────────┘  │
└─────────────────────────┼────────────────────────────────────┘
                          │
                          │ Serial (USB)
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    WEMOS D1 (Hardware)                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  - Receives TRIGGER command                          │  │
│  │  - Moves servo motor                                 │  │
│  │  - Dispenses feed                                    │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘

                          ┌─────────────────┐
                          │  ANIMATION FILE │
                          └────────┬────────┘
                                   │
                                   │ Polls every 500ms
                                   ▼
┌─────────────────────────────────────────────────────────────┐
│                    BROWSER (Frontend)                        │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  JavaScript Polling:                                 │  │
│  │  - Checks /api/feeder/animation                      │  │
│  │  - If active: Show animation                         │  │
│  │  - If not active: Hide animation + reload            │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## ANIMATION LIFECYCLE

```
START
  │
  ├─► User clicks "Feed Now" OR Schedule triggers
  │
  ├─► Laravel writes servo_command.json
  │       {
  │         "action": "trigger",
  │         "duration": 10,
  │         "cage": 1,
  │         "type": "manual"
  │       }
  │
  ├─► Servo Bridge detects command (within 1 second)
  │
  ├─► Servo Bridge creates animation_trigger.json
  │       {
  │         "active": true,
  │         "duration": 10,
  │         "cage": 1,
  │         "started_at": "2025-01-15T08:00:00+08:00"
  │       }
  │
  ├─► Browser polls /api/feeder/animation (every 500ms)
  │
  ├─► Browser detects active=true
  │
  ├─► Browser shows animation
  │       ┌─────────────────────┐
  │       │  🍽️ Dispensing Feed │
  │       │  Cage 1             │
  │       │  [========>    ] 75%│
  │       │  3 seconds remaining│
  │       └─────────────────────┘
  │
  ├─► Servo Bridge sends command to WEMOS
  │
  ├─► WEMOS moves servo for 10 seconds
  │
  ├─► Servo Bridge waits for completion
  │
  ├─► Servo Bridge deletes animation_trigger.json
  │
  ├─► Browser polls /api/feeder/animation
  │
  ├─► Browser detects active=false
  │
  ├─► Browser hides animation (with 1s delay)
  │
  ├─► Browser reloads page
  │
  └─► END (Feed History updated)
```

## FILE FLOW

```
┌──────────────────────┐
│  servo_command.json  │ ◄─── Created by Laravel
└──────────┬───────────┘
           │
           │ Watched by servo_bridge.py
           ▼
┌──────────────────────┐
│   servo_bridge.py    │
│   (Background)       │
└──────────┬───────────┘
           │
           ├─► Creates animation_trigger.json
           │   (Browser polls this)
           │
           ├─► Sends command to WEMOS
           │   (Serial communication)
           │
           ├─► Waits for duration
           │
           ├─► Deletes animation_trigger.json
           │   (Browser detects deletion)
           │
           └─► Writes servo_result.json
               (Result logging)
```

## PROCESS DIAGRAM

```
┌─────────────────────────────────────────────────────────────┐
│                    SYSTEM PROCESSES                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────────┐                                       │
│  │  Apache/XAMPP    │ ◄─── Serves Laravel application       │
│  │  (Port 80)       │                                       │
│  └──────────────────┘                                       │
│                                                              │
│  ┌──────────────────┐                                       │
│  │  pythonw.exe     │ ◄─── Servo Bridge (Background)        │
│  │  (servo_bridge)  │      Watches command file             │
│  └──────────────────┘      Controls servo                   │
│                                                              │
│  ┌──────────────────┐                                       │
│  │  php.exe         │ ◄─── Schedule Checker                 │
│  │  (scheduler)     │      Checks schedules every minute    │
│  └──────────────────┘                                       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## STARTUP SEQUENCE

```
1. User runs: start_feeder_system.bat
   │
   ├─► Checks if pythonw.exe is running
   │   │
   │   ├─► If YES: Skip
   │   └─► If NO: Start servo_bridge.py
   │
   ├─► Starts run_scheduler.bat
   │   │
   │   └─► Runs: php artisan schedule:work
   │
   └─► Shows "ALL SYSTEMS RUNNING!"

2. System is ready
   │
   ├─► Servo Bridge: Watching for commands
   ├─► Scheduler: Checking schedules every minute
   └─► Laravel: Ready to receive requests

3. User can now:
   │
   ├─► Set schedules
   ├─► Trigger manual feeds
   └─► View feed history
```

## DATA FLOW (Manual Feed)

```
User Input
    │
    ├─► Cage: 1
    ├─► Duration: 10 seconds
    └─► Click "Feed Now"
        │
        ▼
    Laravel Controller
        │
        ├─► Validates input
        ├─► Creates ManualFeed record
        └─► Writes servo_command.json
            │
            ▼
    Servo Bridge (Python)
        │
        ├─► Detects command file
        ├─► Creates animation_trigger.json
        ├─► Opens serial connection
        ├─► Sends "TRIGGER 10" to WEMOS
        ├─► Waits 10 seconds
        ├─► Deletes animation_trigger.json
        └─► Writes servo_result.json
            │
            ▼
    WEMOS Hardware
        │
        ├─► Receives command
        ├─► Moves servo 90°
        ├─► Waits 10 seconds
        └─► Moves servo 0°
            │
            ▼
    Feed Dispensed ✅
        │
        ▼
    Browser
        │
        ├─► Detects animation_trigger.json deleted
        ├─► Hides animation
        └─► Reloads page
            │
            ▼
    Feed History Updated ✅
```

## SUMMARY

- **3 Main Components**: Laravel, Servo Bridge, WEMOS
- **2 Key Files**: servo_command.json, animation_trigger.json
- **1 Simple Start**: start_feeder_system.bat
- **0 Manual Steps**: Everything is automatic!

```
┌─────────────────────────────────────────┐
│  START: start_feeder_system.bat         │
│  USE: Feeder Management page            │
│  RESULT: Automatic feeding + animation  │
└─────────────────────────────────────────┘
```
