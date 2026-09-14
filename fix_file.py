with open('resources/views/inventory.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace ₱. with ?.
content = content.replace('₱.', '?.')

with open('resources/views/inventory.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Fixed all ₱. to ?.')
