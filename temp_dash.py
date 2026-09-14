import re
with open('resources/views/customer/dashboard.blade.bak.php', 'r', encoding='utf-16') as f:
    text = f.read()

d_sec = re.search(r'<!-- Dashboard Section -->(.*?)<!-- My Profile Section -->', text, re.DOTALL).group(1)

n_dd_match = re.search(r'<!-- Notification Dropdown -->(.*?)<!-- Logout Modal -->', text, re.DOTALL)
n_dd = n_dd_match.group(1) if n_dd_match else ''

script_raw = ''
script_match = re.search(r'(let notifInt;.*)', text, re.DOTALL)
if script_match:
    script_raw = script_match.group(1)
    script_raw = script_raw.split('</script>')[0] # remove tags after it
    
script_raw = re.sub(r'// Section switching functions.*?// Render chart', '// Render chart', script_raw, flags=re.DOTALL)

out = [
    "@extends('customer.layout')",
    "@section('page-title', 'Dashboard')",
    "@section('active-nav', 'dashboard')",
    "@section('styles')",
    "<script src=\"https://cdn.tailwindcss.com\"></script>",
    "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css\">",
    "<script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script>",
    "@endsection",
    "@section('customer-content')",
    "<div class=\"p-6\">",
    d_sec,
    "</div>",
    n_dd,
    "<script>",
    "function toggleNotif() {",
    "    const dd = document.getElementById('notifDropdown');",
    "    dd.classList.toggle('hidden');",
    "    if (!dd.classList.contains('hidden')) loadNotif();",
    "}",
    script_raw,
    "</script>",
    "@endsection"
]

with open('resources/views/customer/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))
