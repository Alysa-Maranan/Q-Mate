<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt - {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f5f0eb;
            color: #4e342e;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            border-bottom: 2px solid #d7ccc8;
            padding: 1rem 3rem;
            box-shadow: 0 4px 20px rgba(121,85,72,0.08);
        }

        .header-container {
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #a1887f;
            box-shadow: 0 2px 8px rgba(121,85,72,0.18);
        }

        .logo span {
            font-size: 1.35rem;
            font-weight: 800;
            color: #6d4c41;
            letter-spacing: 0.5px;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-name {
            font-weight: 600;
            color: #4e342e;
        }

        .main-wrapper {
            flex: 1;
            padding: 2rem;
        }

        .receipt-container {
            max-width: 450px;
            margin: 0 auto;
            background: white;
            border: 2px dashed #6d4c41;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 4px 20px rgba(109,76,65,0.12);
        }
        
        .receipt-header {
            text-align: center;
            padding: 1.5rem 1rem;
            border-bottom: 2px dashed #6d4c41;
            background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%);
            border-radius: 14px 14px 0 0;
        }
        
        .store-name {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #4e342e;
        }
        
        .store-info {
            font-size: 0.75rem;
            line-height: 1.4;
            margin-top: 0.5rem;
            color: #8d6e63;
        }
        
        .divider {
            border-bottom: 2px dashed #6d4c41;
            margin: 0;
        }
        
        .receipt-body {
            padding: 1rem;
        }
        
        .receipt-section {
            margin-bottom: 1rem;
            border-bottom: 1px dashed #d7ccc8;
            padding-bottom: 0.8rem;
        }
        
        .receipt-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .receipt-line {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
            word-break: break-word;
        }
        
        .receipt-label {
            font-weight: 600;
            color: #6d4c41;
            width: 40%;
        }
        
        .receipt-value {
            text-align: right;
            width: 60%;
            word-wrap: break-word;
            color: #4e342e;
        }
        
        .receipt-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            color: #4e342e;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 6px;
            margin-top: 0.3rem;
        }
        
        .status-pending { 
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #17a2b8;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #28a745;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-shipped {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .receipt-footer {
            text-align: center;
            padding: 1rem;
            border-top: 2px dashed #6d4c41;
            background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%);
            border-radius: 0 0 14px 14px;
            font-size: 0.75rem;
            line-height: 1.6;
            color: #6d4c41;
        }
        
        .thank-you {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #4e342e;
            font-size: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 0 1rem 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-dashboard {
            background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%);
            color: white;
        }
        
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109, 76, 65, 0.3);
        }
        
        .btn {
            flex: 0 0 auto;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #8d6e63 0%, #6d4c41 100%);
            color: white;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109, 76, 65, 0.3);
        }

        .footer {
            background: #4e342e;
            color: #ffe0b2;
            padding: 2.5rem 2rem 1.5rem;
            margin-top: auto;
        }

        .footer-inner {
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,224,178,0.15);
        }

        .footer-brand-name {
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffe0b2;
            margin-bottom: 0.5rem;
        }

        .footer-brand-desc {
            font-size: 0.85rem;
            color: rgba(255,224,178,0.7);
            line-height: 1.6;
        }

        .footer-col-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffe0b2;
            margin-bottom: 0.85rem;
        }

        .footer-col a {
            display: block;
            color: rgba(255,224,178,0.7);
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #fffdfa;
        }

        .footer-contact-line {
            font-size: 0.85rem;
            color: rgba(255,224,178,0.7);
            margin-bottom: 0.5rem;
        }

        .footer-bottom {
            margin: 1.25rem auto 0;
            padding: 0 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: rgba(255,224,178,0.5);
        }
        
        @media print {
            body { padding: 0; background: white; }
            .header, .footer, .action-buttons { display: none !important; }
            .main-wrapper { padding: 0; }
            .receipt-container { max-width: 100%; box-shadow: none; border: 2px dashed #333; }
        }
        
        @media (max-width: 768px) {
            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-inner {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container" style="display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem;">
            <div style="flex: 1; min-width: 250px;">
                <h2 style="font-size: 1.3rem; font-weight: 800; color: #4e342e; margin: 0; white-space: nowrap;">Welcome back, {{ $order->name }}!</h2>
            </div>
            <div style="flex: 2; max-width: 500px; display: flex; justify-content: center;">
                <form action="{{ route('order') }}" method="GET" style="display: flex; position: relative; width: 100%;">
                    <input type="text" name="search" placeholder="Search for products..." style="width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 2px solid #eaddd7; border-radius: 20px; font-size: 0.9rem; outline: none; transition: border-color 0.2s; color: #4e342e;">
                    <i class="ph-bold ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a1887f; font-size: 1.2rem;"></i>
                    <button type="submit" style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); background: #6d4c41; color: white; border: none; padding: 0.3rem 0.8rem; border-radius: 16px; cursor: pointer; font-size: 0.8rem; font-weight: 600;">Search</button>
                </form>
            </div>
            <div style="flex: 1; display: flex; align-items: center; justify-content: flex-end;">
                <div style="position: relative; margin-right: 1.5rem;" id="headerNotifContainer">
                    <button onclick="toggleNotif(event)" style="position: relative; background: transparent; border: none; width: 48px; height: 48px; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='#fcfafa';" onmouseout="this.style.background='transparent';">
                        <div style="position: relative;">
                            <i class="ph-bold ph-bell" style="font-size: 1.6rem; color: #6d4c41; filter: drop-shadow(0 2px 4px rgba(109,76,65,0.2)); transition: all 0.2s;"></i>
                            <span id="notifBadge" style="position:absolute; top:-2px; right:-2px; background:linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%); color:white; border-radius:50%; width:20px; height:20px; font-size:11px; font-weight:700; display:none; align-items:center; justify-content:center; border: 2px solid white; box-shadow: 0 2px 6px rgba(211,47,47,0.4); animation: pulse 2s infinite;">0</span>
                        </div>
                    </button>
                    <div id="notifDropdown" style="position: absolute; top: calc(100% + 5px); right: -10px; background: white; border: 1px solid #eaddd7; border-radius: 12px; box-shadow: 0 10px 25px rgba(109,76,65,0.15); width: 320px; z-index: 10000; display: none; overflow: hidden; transform-origin: top right;">
                        <div style="padding: 1rem; border-bottom: 1px solid #efebe9; display: flex; justify-content: space-between; align-items: center; background: #fffbfa;">
                            <span style="font-weight: 700; color: #4e342e; font-size: 0.95rem;">Notifications</span>
                            <button onclick="clearNotif()" style="background: none; border: none; font-size: 0.8rem; color: #8d6e63; cursor: pointer; text-decoration: underline; padding: 0;">Clear All</button>
                        </div>
                        <div id="notifList" style="max-height: 350px; overflow-y: auto;">
                            <div style="padding: 2rem; text-align: center; color: #a1887f;">
                                <i class="ph-bold ph-bell" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                                <p style="font-size: 0.9rem; margin: 0;">No notifications</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="position: relative;" id="headerUserMenuContainer">
                    <style>
                        .user-dropdown-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.2rem; text-decoration: none; color: #5d4037; font-weight: 600; font-size: 0.95rem; width: 100%; text-align: left; background: none; border: none; cursor: pointer; transition: background-color 0.2s ease, color 0.2s ease; }
                        .user-dropdown-link:hover { background-color: #f5ebe6; color: #3e2723; }
                        .user-dropdown-logout { color: #d32f2f; }
                        .user-dropdown-logout:hover { background-color: #ffebee; color: #b71c1c; }
                        .user-dropdown-link i { font-size: 1.2rem; width: 24px; text-align: center; }
                    </style>
                    <div class="header-user" style="display: flex; align-items: center; gap: 1rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#fcfafa'" onmouseout="this.style.background='transparent'" onclick="toggleUserDropdown(event)">
                        <div class="user-avatar">{{ substr($order->name, 0, 1) }}</div>
                        <div style="display: flex; flex-direction: column; align-items: flex-start;">
                            <span class="user-name" style="font-weight: 600; color: #4e342e;">{{ $order->name }}</span>
                            <span style="font-size: 0.85rem; color: #8d6e63; font-weight: 500;">Customer <i class="ph-bold ph-caret-down" style="font-size: 0.7rem;"></i></span>
                        </div>
                    </div>
                    <div id="userDropdownMenu" style="position: absolute; top: calc(100% + 5px); right: 0; background: white; border: 1px solid #eaddd7; border-radius: 12px; box-shadow: 0 10px 25px rgba(109,76,65,0.15); width: 230px; z-index: 9999; display: none; overflow: hidden; transform-origin: top right;">
                        <a href="{{ route('customer.profile') }}" class="user-dropdown-link"><i class="ph-bold ph-user"></i> My Profile</a>
                        <a href="{{ route('customer.orders.all') }}" class="user-dropdown-link"><i class="ph-bold ph-package"></i> My Orders</a>
                        <div style="height: 1px; background: #efebe9; margin: 0;"></div>
                        <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" class="user-dropdown-link user-dropdown-logout"><i class="ph-bold ph-sign-out"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-wrapper">
        <div class="receipt-container">
            <!-- Header -->
            <div class="receipt-header">
                <div class="store-name">ESCALONA'S FARM</div>
                <div class="store-info">
                    Pagkakaisa, Naujan<br>
                    Oriental Mindoro<br>
                    <br>
                    Tel: +63 917 123 4567<br>
                    Email: escalona.farm@gmail.com
                </div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Body -->
            <div class="receipt-body">
                <!-- Order Info -->
                <div class="receipt-section">
                    <div class="receipt-title">Receipt</div>
                    <div class="receipt-line">
                        <span class="receipt-label">Order #:</span>
                        <span class="receipt-value">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Date:</span>
                        <span class="receipt-value">{{ $order->created_at->format('m/d/Y') }}</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Time:</span>
                        <span class="receipt-value">{{ $order->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="receipt-line" style="margin-top: 0.5rem;">
                        <span class="receipt-label">Status:</span>
                        <span class="receipt-value">
                            <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </span>
                    </div>
                </div>
                
                <!-- Customer Info -->
                <div class="receipt-section">
                    <div class="receipt-title">Customer</div>
                    <div class="receipt-line">
                        <span class="receipt-label">Name:</span>
                        <span class="receipt-value">{{ $order->name }}</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Phone:</span>
                        <span class="receipt-value">{{ $order->phone }}</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Email:</span>
                        <span class="receipt-value" style="font-size: 0.75rem;">{{ $order->email }}</span>
                    </div>
                    @if($order->address)
                    <div class="receipt-line">
                        <span class="receipt-label">Address:</span>
                        <span class="receipt-value">{{ $order->address }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Product Details -->
                <div class="receipt-section">
                    <div class="receipt-title">Product</div>
                    <div class="receipt-line">
                        <span class="receipt-label">Item:</span>
                        <span class="receipt-value">{{ ucwords(str_replace('_', ' ', $order->product)) }}</span>
                    </div>
                    <div class="receipt-line">
                        <span class="receipt-label">Qty:</span>
                        <span class="receipt-value">{{ $order->quantity }}</span>
                    </div>
                </div>
                
                @if($order->notes)
                <!-- Notes -->
                <div class="receipt-section">
                    <div class="receipt-title">Notes</div>
                    <div style="font-size: 0.85rem; line-height: 1.4; word-wrap: break-word; color: #4e342e;">{{ $order->notes }}</div>
                </div>
                @endif
                
                @if($order->cancellation_reason)
                <!-- Cancellation Reason -->
                <div class="receipt-section">
                    <div class="receipt-title">Cancellation Reason</div>
                    <div style="font-size: 0.85rem; line-height: 1.4; word-wrap: break-word; color: #d32f2f;">{{ $order->cancellation_reason }}</div>
                </div>
                @endif
            </div>
            
            <div class="divider"></div>
            
            <!-- Footer -->
            <div class="receipt-footer">
                <div class="thank-you">THANK YOU</div>
                <div>for your order!</div>
                <div style="margin-top: 0.8rem; font-size: 0.7rem; color: #8d6e63;">
                    Powered by SQUIFM<br>
                    Smart Quail Management System
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-dashboard">
                <i class="ph-bold ph-house"></i> Back to Dashboard
            </a>
            <button class="btn btn-print" onclick="window.print()">
                <i class="ph-bold ph-printer"></i> Print Receipt
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <div>
                <div class="footer-brand-name">Escalona's Farm</div>
                <div class="footer-brand-desc">An IoT-powered Smart Quail Management System designed to make farm life more efficient and produce higher quality yields.</div>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Quick Links</div>
                <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                <a href="{{ route('customer.profile') }}">My Profile</a>
                <a href="{{ route('order') }}">Browse Products</a>
                <a href="{{ route('customer.orders') }}">All Orders</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Account</div>
                <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                <a href="{{ route('customer.profile') }}">My Profile</a>
                <a href="{{ route('customer.orders') }}">My Orders</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Contact Info</div>
                <div class="footer-contact-line">📍 Pagkakaisa, Naujan, Or. Mindoro</div>
                <div class="footer-contact-line">📞 +63 917 123 4567</div>
                <div class="footer-contact-line">📧 escalona.farm@gmail.com</div>
            </div>
        </div>
        <div class="footer-bottom">&copy; {{ date('Y') }} Escalona's Farm | Capstone SQUIFM. All rights reserved.</div>
    </footer>

    <script>
        function toggleNotif(e) {
            e.stopPropagation();
            const dd = document.getElementById('notifDropdown');
            if (dd.style.display === 'none' || dd.style.display === '') {
                dd.style.display = 'block';
                loadNotif();
            } else {
                dd.style.display = 'none';
            }
        }

        async function loadNotif() {
            try {
                const res = await fetch('/api/customer/notifications');
                const data = await res.json();
                const badge = document.getElementById('notifBadge');
                const list = document.getElementById('notifList');
                
                if (data.notifications && data.notifications.length > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
                    list.innerHTML = data.notifications.map(n => `
                        <div style="padding: 1rem; border-bottom: 1px solid #efebe9; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#fcfafa'" onmouseout="this.style.background='transparent'" onclick="window.location.href='{{ route('customer.orders.all') }}'">
                            <p style="font-size: 0.85rem; font-weight: 600; color: #4e342e; margin: 0 0 0.25rem 0; line-height: 1.3;">${n.message} (${n.order_number})</p>
                            <p style="font-size: 0.75rem; color: #8d6e63; margin: 0;">${n.time}</p>
                        </div>
                    `).join('');
                } else {
                    badge.style.display = 'none';
                    list.innerHTML = '<div style="padding: 2rem; text-align: center; color: #a1887f;"><i class="ph-bold ph-bell" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.3;"></i><p style="font-size: 0.9rem; margin: 0;">No notifications</p></div>';
                }
            } catch (e) {}
        }

        async function clearNotif() {
            try {
                await fetch('/api/customer/notifications/mark-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                document.getElementById('notifBadge').style.display = 'none';
                loadNotif();
            } catch (e) {}
        }

        function toggleUserDropdown(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('userDropdownMenu');
            dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
        }

        document.addEventListener('click', function(e) {
            const userDropdown = document.getElementById('userDropdownMenu');
            const userContainer = document.getElementById('headerUserMenuContainer');
            if (userDropdown && userDropdown.style.display === 'block' && !userContainer.contains(e.target)) {
                userDropdown.style.display = 'none';
            }
            const notifDropdown = document.getElementById('notifDropdown');
            const notifContainer = document.getElementById('headerNotifContainer');
            if (notifDropdown && notifDropdown.style.display === 'block' && !notifContainer.contains(e.target)) {
                notifDropdown.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadNotif();
            setInterval(loadNotif, 30000);
        });

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(211, 47, 47, 0); }
            100% { box-shadow: 0 0 0 0 rgba(211, 47, 47, 0); }
        }
    </script>
</body>
</html>