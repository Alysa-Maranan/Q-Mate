import re

with open('inventory.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace all remaining broken operators
# ₱₱ -> ??
content = content.replace('\u20b1\u20b1', '??')
# ₱ [space] -> ? [space] (ternary)
content = re.sub(r'₱\s+', '? ', content)
# ₱: -> ?: (ternary)
content = content.replace('\u20b1:', '?:')
# ₱. -> ?. (optional chaining)
content = content.replace('\u20b1.', '?.')

with open('inventory.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Fixed all broken operators')
