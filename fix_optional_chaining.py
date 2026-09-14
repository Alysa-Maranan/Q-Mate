#!/usr/bin/env python3
# -*- coding: utf-8 -*-

with open('resources/views/inventory.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Count instances before
before = content.count('₱.')
print(f'Found {before} instances of ₱.')

# Replace ₱. with ?.
content = content.replace('₱.', '?.')

# Count instances after
after = content.count('₱.')
print(f'After replacement, found {after} instances of ₱.')

with open('resources/views/inventory.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('File updated successfully')
