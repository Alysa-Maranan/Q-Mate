# 🍽️ Daily Feeding Auto-Record Guide

## Paano Gumana ang Daily Feeding History

Ang Daily Feeding Schedule timeline sa UI ay nag-display ng 3 feeding times:
- 🌅 Morning: 08:00 AM
- ☀️ Afternoon: 12:00 PM  
- 🌇 Evening: 05:00 PM

Para mag-record sa Feed History, kailangan:
1. ✅ May schedules sa database (AUTO-CREATE NA)
2. ✅ May auto-check system na nag-trigger ng feeding

---

## Step 1: Visit Feeder Page (AUTO-SETUP)

Pag binuksan mo ang Feeder page, **AUTOMATIC** na nag-create ng 3 schedules:

```
http://localhost/feeder
```

Ang system ay mag-create ng:
- Morning schedule (08:00)
- Afternoon schedule (12:00)
- Evening schedule (17:00)

**TAPOS NA! Schedules are ready!** ✅

---

## Step 2: Start Auto-Check System

Para mag-trigger ang feeding automatically, i-run ang batch file:

```bash
start_daily_feeding_check.bat
```

Ito ay:
- Nag-check every 60 seconds
- Kapag may schedule na match sa current time, nag-trigger ng feeding
- Nag-record sa Feed History ✅

---

## How It Works

### Automatic Flow:

1. **Page Load** → Auto-create schedules (kung wala pa)
2. **Auto-Check** (every 60 seconds) → Check kung may schedule na match
3. **Match Found** → Trigger feeding
4. **Background Command** → `feeder:schedule-done` runs
5. **Record to History** → FeedHistory::create() ✅

### Example:

```
08:00 AM - Auto-check runs
         → Found schedule for 08:00
         → Trigger feeding (20 seconds)
         → Record to feed_history:
            - feed_type: 'scheduled'
            - feed_brand: 'Quail Layer Smash'
            - duration: 20
            - cage_number: 1
            - status: 'completed'
            - fed_at: 2025-01-XX 08:00:00
```

---

## Verify It's Working

### Check Schedules:
```bash
php artisan tinker
\App\Models\FeedingSchedule::all();
```

Dapat may 3 schedules (08:00, 12:00, 17:00)

### Check History:
```bash
php artisan tinker
\App\Models\FeedHistory::latest()->take(5)->get();
```

Dapat may records after feeding time

### Check Logs:
```bash
type storage\logs\laravel.log | findstr "schedule"
```

---

## Troubleshooting

### Hindi nag-rerecord sa history?

**Check 1:** May schedules ba?
```bash
php artisan tinker
\App\Models\FeedingSchedule::count();
```
Dapat at least 3

**Check 2:** Running ba ang auto-check?
```bash
curl http://localhost/feeder/schedules/auto-check
```
Dapat may response: `{"status":"ok","triggered":[],"count":0}`

**Check 3:** Tama ba ang time?
```bash
php artisan tinker
now()->format('H:i');
```
Kung 08:00, dapat mag-trigger

### Gusto i-reset lahat?

```bash
php artisan tinker
\App\Models\FeedingSchedule::truncate();
```

Then visit feeder page ulit para mag-auto-create

---

## Summary

✅ **Auto-Create** - Schedules are created automatically when you visit feeder page
✅ **Auto-Check** - Run `start_daily_feeding_check.bat` to check every 60 seconds
✅ **Auto-Record** - Feeding history is recorded automatically
✅ **Type Column** - Shows "Scheduled" for daily feeding
✅ **Brand Column** - Shows current feed brand

**PAG NAG-CHECK NA YUNG ICON SA DAILY FEEDING SCHEDULE, PAPASOK NA SA FEED HISTORY!** 🎉

---

## Quick Start (2 Steps Only!)

1. Visit: `http://localhost/feeder` (auto-creates schedules)
2. Run: `start_daily_feeding_check.bat` (starts auto-check)

**TAPOS NA!** ✅
