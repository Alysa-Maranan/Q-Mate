#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os

# Change to the views directory
os.chdir('c:\\xampp\\htdocs\\CAPSTONE SQUIFM\\resources\\views')

with open('inventory.blade.php', 'rb') as f:
    content = f.read()

# Decode to string
content_str = content.decode('utf-8')

# Replace all broken peso signs with question marks
# This handles: ₱₱ -> ??, ₱ (space) -> ? (space), ₱: -> ?:, ₱. -> ?.
content_str = content_str.replace('\u20b1\u20b1', '??')  # Null coalescing
content_str = content_str.replace('\u20b1 ', '? ')       # Ternary with space
content_str = content_str.replace('\u20b1:', '?:')       # Ternary alt form
content_str = content_str.replace('\u20b1.', '?.')       # Optional chaining

# Encode back to bytes and write
with open('inventory.blade.php', 'wb') as f:
    f.write(content_str.encode('utf-8'))

print(f'✓ File fixed successfully')
