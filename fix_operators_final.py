#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys

file_path = r'c:\xampp\htdocs\CAPSTONE SQUIFM\resources\views\inventory.blade.php'

# Read file with UTF-8 encoding
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Count before
count_before = content.count('\u20b1')
print(f'Before: {count_before} instances of peso sign ₱', file=sys.stderr)

# Replace all variations - order matters! Do longest first
content = content.replace('\u20b1\u20b1', '??')  #₱₱ → ??
content = content.replace('\u20b1 ', '? ')      # ₱ [space] → ? [space]
content = content.replace('\u20b1:', '?:')      # ₱: → ?:
content = content.replace('\u20b1.', '?.')      # ₱. → ?.
content = content.replace('\u20b1', '?')        # any remaining ₱ → ?

# Count after
count_after = content.count('\u20b1')
print(f'After: {count_after} instances of peso sign ₱', file=sys.stderr)

# Write file with UTF-8 encoding
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print('Done! Replaced {} instances'.format(count_before - count_after), file=sys.stderr)
