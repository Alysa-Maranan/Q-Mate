# EXACT TIMING FEEDING SYSTEM

## Problema na Naayos:
- Dati: May delay o advance ng ilang seconds
- Ngayon: Mag-trigger EXACTLY sa scheduled time (10:51:00 - 10:51:05)

## Paano Gumana:

### 1. Schedule Checker
- Tumatakbo every **1 SECOND** para sa precise timing
- Nag-check kung:
  - Current time = scheduled time (H:i format)
  - Current seconds = 0-5 seconds (start of minute)
- Kapag BOTH conditions match, INSTANT trigger ng servo at animation

### 2. Time Matching Logic
- Format: H:i (example: 10:51, 14:30, 18:00)
- Seconds check: 0-5 seconds lang (10:51:00 to 10:51:05)
- Bakit 0-5 seconds? Para ma-catch ang exact start ng minute
- May duplicate prevention (hindi mag-trigger ulit sa same minute)

### 3. Paano Gamitin:

#### Option 1: Gamit ang Exact Timing Feeder (Recommended)
```bash
start_exact_feeding.bat
```
- Ito ay mag-check every 1 second
- Automatic na mag-trigger kapag dumating ang scheduled time
- Only triggers within first 5 seconds of the minute

#### Option 2: Manual Check
```bash
php artisan feeder:check-automatic
```

### 4. Pag-set ng Schedule:

Sa Settings page, i-set ang feeding times:
- Morning: 08:00 (or kahit anong oras)
- Afternoon: 12:00
- Evening: 17:00

### 5. Testing:

1. I-set ang schedule sa Settings (example: 10:51)
2. Run ang `start_exact_feeding.bat`
3. Maghintay hanggang 10:51:00
4. Dapat EXACTLY 10:51:00-10:51:05 lalabas ang animation at mag-open ang servo

### 6. Logs:

Check ang logs para makita kung nag-trigger:
```
storage/logs/laravel.log
```

Makikita mo:
```
EXACT TIME MATCH! Triggering feeding NOW at 10:51:02
Automatic feeding triggered IMMEDIATELY at 10:51:02
Automatic feeding completed at 10:51
```

## Technical Details:

- **Check Interval**: Every 1 second
- **Time Format**: H:i (24-hour format, no seconds)
- **Trigger Window**: 0-5 seconds of the scheduled minute
- **Duplicate Prevention**: Uses `last_auto_feed.txt` file
- **Animation**: Automatic (type: 'scheduled')
- **Duration**: 20 seconds default

## Bakit 0-5 Seconds Window?

**Scenario 1: Kung 10:51:00 exactly**
- Check at 10:51:00 → ✅ TRIGGER!

**Scenario 2: Kung 10:51:03**
- Check at 10:51:03 → ✅ TRIGGER! (within 5 seconds)

**Scenario 3: Kung 10:51:06**
- Check at 10:51:06 → ❌ SKIP (already past 5 seconds)

**Scenario 4: Kung 10:50:55**
- Check at 10:50:55 → ❌ SKIP (not yet 10:51)

Sa ganitong logic, sigurado na:
- Hindi mag-trigger ng maaga (advance)
- Hindi mag-trigger ng late (delay)
- Mag-trigger lang EXACTLY sa start ng minute

## Troubleshooting:

### Kung nag-advance pa rin:
1. Check ang server time: `php artisan tinker` then `now()`
2. Verify na tama ang timezone sa `.env`: `APP_TIMEZONE=Asia/Manila`
3. Check logs kung anong seconds nag-trigger

### Kung may delay pa rin:
1. Siguraduhing tumatakbo ang `start_exact_feeding.bat`
2. Check kung may error sa logs
3. Verify na working ang servo_bridge.py

### Kung nag-trigger ng multiple times:
1. Check ang `last_auto_feed.txt` file
2. Verify na may duplicate prevention

## IMPORTANTE:

**DAPAT LAGING TUMATAKBO ANG `start_exact_feeding.bat`**

Kung hindi tumatakbo, walang mag-check at walang mag-trigger ng feeding!

## Example Timeline:

```
10:50:58 → Check → No match (not yet 10:51)
10:50:59 → Check → No match (not yet 10:51)
10:51:00 → Check → ✅ MATCH! TRIGGER NOW!
10:51:01 → Check → Already triggered (duplicate prevention)
10:51:02 → Check → Already triggered (duplicate prevention)
...
10:51:59 → Check → Already triggered (duplicate prevention)
10:52:00 → Check → No match (not scheduled time)
```
