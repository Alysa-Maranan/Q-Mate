import re

file_path = r'c:\xampp\htdocs\CAPSTONE SQUIFM\resources\views\welcome2.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Replace Order Now buttons
old_btn = r'<a href="{{ route\(\'products\'\) }}" class="btn btn-outline product-action">Order Now</a>'
new_btn = r'<button onclick="openOrderModal()" class="btn btn-outline product-action">Order Now</button>'
content = re.sub(old_btn, new_btn, content)

# 2. Add CSS before /* RESPONSIVE */
css_addition = """
        /* MODAL */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; z-index: 2000; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-content { background: white; padding: 2.5rem; border-radius: 20px; max-width: 450px; width: 90%; text-align: center; transform: translateY(30px); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 15px 40px rgba(0,0,0,0.2); position: relative; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        .modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; transition: color 0.2s; }
        .modal-close:hover { color: var(--accent-hover); }
        .modal-icon { font-size: 3.5rem; color: var(--accent); margin-bottom: 1rem; }
        .modal-content h3 { font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--primary-dark); }
        .modal-content p { color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.6; }
        .modal-buttons { display: flex; flex-direction: column; gap: 0.8rem; }

        /* RESPONSIVE */"""
content = content.replace('/* RESPONSIVE */', css_addition)

# 3. Add Modal HTML and JS before </body>
modal_html = """
<!-- ORDER MODAL -->
<div class="modal-overlay" id="orderModal" onclick="closeOrderModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeOrderModal()"><i class="ph-bold ph-x"></i></button>
        <i class="ph-fill ph-shopping-cart modal-icon"></i>
        <h3>Customer Account Required</h3>
        <p>To ensure a smooth delivery and properly track your farm fresh orders, please create a customer account or login to continue purchasing.</p>
        <div class="modal-buttons">
            <a href="#" class="btn btn-primary"><i class="ph-bold ph-user-plus"></i> Create Account</a>
            <a href="#" class="btn btn-outline"><i class="ph-bold ph-sign-in"></i> Login existing account</a>
        </div>
    </div>
</div>

<script>
function openOrderModal() {
    document.getElementById('orderModal').classList.add('active');
}

function closeOrderModal(e) {
    if(!e || e.target.classList.contains('modal-overlay') || e.currentTarget.classList.contains('modal-close')) {
        document.getElementById('orderModal').classList.remove('active');
    }
}
</script>

</body>"""
content = content.replace('</body>', modal_html)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Modal added successfully.")
