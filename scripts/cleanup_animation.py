#!/usr/bin/env python
"""
Cleanup animation file after duration
Usage: python cleanup_animation.py <duration_seconds>
"""
import sys
import time
import os
import json

if len(sys.argv) < 2:
    print("Usage: python cleanup_animation.py <duration_seconds>")
    sys.exit(1)

duration = int(sys.argv[1])
base_path = os.path.join(os.path.dirname(__file__), '..', 'storage', 'logs')
anim_file = os.path.join(base_path, 'animation_trigger.json')

# Wait for duration
time.sleep(duration)

# Delete animation file
if os.path.exists(anim_file):
    os.remove(anim_file)
    print(f"Animation file deleted after {duration}s")
