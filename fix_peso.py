#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys

path = 'resources/views/inventory.blade.php'

try:
    # Read the file
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Show what we're replacing
    peso_dot_count = content.count('\u20b1.')
    print(f'Found {peso_dot_count} instances of peso-dot', file=sys.stderr)
    
    # Replace peso sign + dot with question mark + dot
    content = content.replace('\u20b1.', '?.')
    
    # Verify
    after_count = content.count('\u20b1.')
    print(f'After replacement: {after_count} instances of peso-dot', file=sys.stderr)
    
    # Write back
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print('Successfully fixed optional chaining operators', file=sys.stderr)
    
except Exception as e:
    print(f'Error: {e}', file=sys.stderr)
    sys.exit(1)
