import re
with open('resources/views/customer/profile.blade.bak.php', 'r', encoding='utf-16') as f:
    text = f.read()

content_body = re.search(r'<div class="content-body">(.*?)</div>\s*</main>', text, re.DOTALL).group(1)

out = [
    "@extends('customer.layout')",
    "@section('page-title', 'My Profile')",
    "@section('active-nav', 'profile')",
    "",
    "@section('header-action')",
    "<button class='edit-btn' id='editBtn' onclick='toggleEdit()'>",
    "    <i class='ph-bold ph-pencil-simple'></i>",
    "    Edit Profile",
    "</button>",
    "@endsection",
    "",
    "@section('customer-content')",
    "<style>",
    "    .edit-btn { padding: 0.5rem 1rem; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; position: absolute; right: 1.5rem; }",
    "    .edit-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109,76,65,0.3); }",
    "    .content-body { padding: 2rem; }",
    "    .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }",
    "    .form-group { margin-bottom: 1.5rem; }",
    "    .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: #4e342e; margin-bottom: 0.5rem; }",
    "    .form-input { width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem; font-family: inherit; transition: all 0.2s; }",
    "    .form-input:focus { outline: none; border-color: #a1887f; box-shadow: 0 0 0 3px rgba(161,136,127,0.1); }",
    "    .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }",
    "    .btn-primary { background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; }",
    "    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109,76,65,0.3); }",
    "    .btn-secondary { background: #f5f0eb; color: #6d4c41; border: 1px solid #d7ccc8; }",
    "    .btn-secondary:hover { background: #efebe9; }",
    "    .hidden { display: none; }",
    "</style>",
    "<div class='content-body'>",
    content_body,
    "</div>",
    "<script>",
    "    function toggleEdit() {",
    "        const viewMode = document.getElementById('viewMode');",
    "        const editMode = document.getElementById('editMode');",
    "        const editBtn = document.getElementById('editBtn');",
    "        if (viewMode.classList.contains('hidden')) {",
    "            viewMode.classList.remove('hidden');",
    "            editMode.classList.add('hidden');",
    "            editBtn.innerHTML = '<i class=\"ph-bold ph-pencil-simple\"></i> Edit Profile';",
    "        } else {",
    "            viewMode.classList.add('hidden');",
    "            editMode.classList.remove('hidden');",
    "            editBtn.innerHTML = '<i class=\"ph-bold ph-x\"></i> Cancel';",
    "        }",
    "    }",
    "</script>",
    "@endsection"
]

with open('resources/views/customer/profile.blade.php', 'w', encoding='utf-8') as f:
    f.write('\n'.join(out))
