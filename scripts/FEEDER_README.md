# Feeder integration (quick steps)

1. Ensure your ESP32 is flashed with `scripts/esp32_servo.ino` and connected via USB.
2. Connect servo signal to the pin used in the sketch (default GPIO 13). Servo power should be an external 5V supply; connect grounds together.
3. Install Python dependencies:

```bash
pip install -r requirements.txt
```

4. Set serial port in your `.env` (Windows example):

```
SERVO_SERIAL_PORT=COM3
```

5. Add a feeding schedule in the web UI: visit `/feeder/schedules`.

6. To test trigger from the app manually:

```bash
php artisan feeder:check --id=1
```

7. To test directly with Python (replace port):

```bash
python scripts/servo_control.py trigger 1 COM3
```

Troubleshooting:
- If the script cannot open the port, check Device Manager for the correct COM number.
- Ensure servo has a stable 5V supply and common ground with ESP32.
- If servo jitters, add a capacitor (1000uF) across 5V and GND near the servo.
