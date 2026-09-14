<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title') | Escalona's Farm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @yield('styles')
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
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
            position: relative;
            z-index: 1;
        }

        .main-container {
            margin: 0;
            padding: 2rem;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
        }

        .sidebar {
            background: white;
            border-radius: 16px;
            padding: 0;
            height: fit-content;
            box-shadow: 0 4px 20px rgba(109,76,65,0.08);
            border: 1px solid rgba(215,204,200,0.5);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #efebe9;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .sidebar-user-info h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #4e342e;
            margin-bottom: 0.25rem;
        }

        .sidebar-user-info p {
            font-size: 0.85rem;
            color: #8d6e63;
        }

        .sidebar-nav {
            padding: 0;
        }

        .nav-section {
            border-bottom: 1px solid #efebe9;
        }

        .nav-section:last-child {
            border-bottom: none;
        }

        .nav-title {
            padding: 1rem 1.5rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #a1887f;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: #4e342e;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: #f5f0eb;
            color: #6d4c41;
        }

        .nav-item.active {
            background: #fff5f5;
            color: #6d4c41;
            border-left-color: #a1887f;
            font-weight: 600;
        }

        .nav-item i {
            font-size: 1.1rem;
            width: 20px;
        }

        .nav-item span {
            font-size: 0.9rem;
        }

        .main-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(109,76,65,0.08);
            border: 1px solid rgba(215,204,200,0.5);
            overflow: hidden;
        }

        .content-header {
            padding: 1.5rem;
            border-bottom: 1px solid #efebe9;
            text-align: center;
        }

        .content-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4e342e;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #f5f0eb;
            background: #faf8f6;
        }

        .tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            color: #8d6e63;
            transition: all 0.2s;
            border-bottom: 3px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .tab:hover {
            background: #f5f0eb;
            color: #6d4c41;
        }

        .tab.active {
            color: #6d4c41;
            background: white;
            border-bottom-color: #a1887f;
        }

        .tab i {
            font-size: 1.1rem;
        }

        .tab-content {
            padding: 2rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #8d6e63;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #4e342e;
        }

        .empty-state p {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .shop-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .shop-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109,76,65,0.3);
        }

        .footer {
            background: #4e342e;
            color: #fff;
            padding: 3rem 5% 1.5rem;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .footer-brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.75rem;
        }

        .footer-brand-desc {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
            max-width: 300px;
        }

        .footer-col-title {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .footer-col a {
            display: block;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #6d4c41;
        }

        .footer-contact {
            list-style: none;
            padding: 0;
        }

        .footer-contact li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }

        .footer-contact i {
            color: #6d4c41;
            font-size: 1.2rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .tab {
                flex: 1 1 50%;
            }

            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .footer-inner {
                grid-template-columns: 1fr;
            }

            .tab {
                flex: 1 1 100%;
            }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(211, 47, 47, 0); }
            100% { box-shadow: 0 0 0 0 rgba(211, 47, 47, 0); }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-container" style="display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem;">
            
            <!-- Welcome Text Section (Left) -->
            <div style="flex: 1; min-width: 250px;">
                <h2 style="font-size: 1.3rem; font-weight: 800; color: #4e342e; margin: 0; white-space: nowrap;">Welcome back, {{ auth('customer')->user()->name }}!</h2>
            </div>

            <!-- Search Bar Section (Center) -->
            <div style="flex: 2; max-width: 500px; display: flex; justify-content: center;">
                <form action="{{ route('order') }}" method="GET" style="display: flex; position: relative; width: 100%;">
                    <input type="text" name="search" placeholder="Search for products..." style="width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 2px solid #eaddd7; border-radius: 20px; font-size: 0.9rem; outline: none; transition: border-color 0.2s; color: #4e342e;">
                    <i class="ph-bold ph-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a1887f; font-size: 1.2rem;"></i>
                    <button type="submit" style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); background: #6d4c41; color: white; border: none; padding: 0.3rem 0.8rem; border-radius: 16px; cursor: pointer; font-size: 0.8rem; font-weight: 600;">Search</button>
                </form>
            </div>

            <!-- Right Side Container (Notifications + User Menu) -->
            <div style="flex: 1; display: flex; align-items: center; justify-content: flex-end;">
            
                <!-- Notifications Section -->
            <div style="position: relative; margin-right: 1.5rem;" id="headerNotifContainer">
                <button onclick="toggleNotif(event)" style="position: relative; background: transparent; border: none; width: 48px; height: 48px; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='#fcfafa';" onmouseout="this.style.background='transparent';">
                    <div style="position: relative;">
                        <i class="ph-bold ph-bell" style="font-size: 1.6rem; color: #6d4c41; filter: drop-shadow(0 2px 4px rgba(109,76,65,0.2)); transition: all 0.2s;"></i>
                        <span id="notifBadge" class="hidden" style="position:absolute; top:-2px; right:-2px; background:linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%); color:white; border-radius:50%; width:20px; height:20px; font-size:11px; font-weight:700; display:none; align-items:center; justify-content:center; border: 2px solid white; box-shadow: 0 2px 6px rgba(211,47,47,0.4); animation: pulse 2s infinite;">0</span>
                    </div>
                </button>

                <!-- Notification Dropdown -->
                <div id="notifDropdown" style="position: absolute; top: calc(100% + 5px); right: -10px; background: white; border: 1px solid #eaddd7; border-radius: 12px; box-shadow: 0 10px 25px rgba(109,76,65,0.15); width: 320px; z-index: 10000; display: none; overflow: hidden; transform-origin: top right;">
                    <div style="padding: 1rem; border-bottom: 1px solid #efebe9; display: flex; justify-content: space-between; align-items: center; background: #fffbfa;">
                        <span style="font-weight: 700; color: #4e342e; font-size: 0.95rem;">Notifications</span>
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            <button onclick="markAllRead()" style="background: none; border: none; font-size: 0.8rem; color: #6d4c41; cursor: pointer; text-decoration: underline; padding: 0; font-weight: 600;">Mark all read</button>
                            <button onclick="clearNotif()" style="background: none; border: none; font-size: 0.8rem; color: #8d6e63; cursor: pointer; text-decoration: underline; padding: 0;">Clear All</button>
                        </div>
                    </div>
                    <div id="notifList" style="max-height: 350px; overflow-y: auto;">
                        <div style="padding: 2rem; text-align: center; color: #a1887f;">
                            <i class="ph-bold ph-bell" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                            <p style="font-size: 0.9rem; margin: 0;">No notifications</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Menu Section (Dropdown trigger setup) -->
            <div style="position: relative;" id="headerUserMenuContainer">
                <style>
                    .user-dropdown-link {
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.85rem 1.2rem;
                        text-decoration: none;
                        color: #5d4037;
                        font-weight: 600;
                        font-size: 0.95rem;
                        width: 100%;
                        text-align: left;
                        background: none;
                        border: none;
                        cursor: pointer;
                        transition: background-color 0.2s ease, color 0.2s ease;
                    }
                    .user-dropdown-link:hover {
                        background-color: #f5ebe6;
                        color: #3e2723;
                    }
                    .user-dropdown-logout {
                        color: #d32f2f;
                    }
                    .user-dropdown-logout:hover {
                        background-color: #ffebee;
                        color: #b71c1c;
                    }
                    .user-dropdown-link i {
                        font-size: 1.2rem;
                        width: 24px;
                        text-align: center;
                    }
                </style>
                <div class="header-user" style="display: flex; align-items: center; gap: 1rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#fcfafa'" onmouseout="this.style.background='transparent'" onclick="toggleUserDropdown(event)">
                    <div class="sidebar-avatar">{{ substr(auth('customer')->user()->name, 0, 1) }}</div>
                    <div style="display: flex; flex-direction: column; align-items: flex-start;">
                        <span class="user-name" style="font-weight: 600; color: #4e342e;">{{ auth('customer')->user()->name }}</span>
                        <span style="font-size: 0.85rem; color: #8d6e63; font-weight: 500;">Customer <i class="ph-bold ph-caret-down" style="font-size: 0.7rem;"></i></span>
                    </div>
                </div>

                <!-- Dropdown Menu -->
                <div id="userDropdownMenu" style="position: absolute; top: calc(100% + 5px); right: 0; background: white; border: 1px solid #eaddd7; border-radius: 12px; box-shadow: 0 10px 25px rgba(109,76,65,0.15); width: 230px; z-index: 9999; display: none; overflow: hidden; transform-origin: top right;">
                    <a href="{{ route('customer.profile') }}" class="user-dropdown-link">
                        <i class="ph-bold ph-user"></i> My Profile
                    </a>
                    <a href="{{ route('customer.orders.all') }}" class="user-dropdown-link">
                        <i class="ph-bold ph-package"></i> My Orders
                    </a>
                    <div style="height: 1px; background: #efebe9; margin: 0;"></div>
                    <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0; width: 100%;">
                        @csrf
                        <button type="submit" class="user-dropdown-link user-dropdown-logout">
                            <i class="ph-bold ph-sign-out"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
            </div> <!-- Close Right Side Container -->
        </div>
    </header>

    <script>
        // ── Customer Notification System ──
        function _readStore() {
            try { return JSON.parse(localStorage.getItem('sqfm_cust_notif_read') || '[]'); }
            catch (e) { return []; }
        }
        function _saveRead(ids) {
            try { localStorage.setItem('sqfm_cust_notif_read', JSON.stringify(ids)); } catch (e) {}
        }
        function _dismissedStore() {
            try { return JSON.parse(localStorage.getItem('sqfm_cust_notif_dismissed') || '[]'); }
            catch (e) { return []; }
        }
        function _saveDismissed(ids) {
            try { localStorage.setItem('sqfm_cust_notif_dismissed', JSON.stringify(ids)); } catch (e) {}
        }

        function playBell() {
            try {
                var AC = window.AudioContext || window.webkitAudioContext;
                if (!AC) return;
                var ctx = new AC();
                if (ctx.state === 'suspended') ctx.resume();
                [[880, 0], [660, 0.18]].forEach(function (f) {
                    var osc = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    var t0 = ctx.currentTime + f[1];
                    osc.frequency.setValueAtTime(f[0], t0);
                    gain.gain.setValueAtTime(0.0001, t0);
                    gain.gain.linearRampToValueAtTime(0.5, t0 + 0.04);
                    gain.gain.exponentialRampToValueAtTime(0.001, t0 + 0.9);
                    osc.start(t0);
                    osc.stop(t0 + 0.9);
                });
            } catch (e) {}
        }

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
                var res = await fetch('/api/customer/notifications');
                var data = await res.json();
                var badge = document.getElementById('notifBadge');
                var list = document.getElementById('notifList');
                var readIds = _readStore();
                var fresh = data.notifications || [];
                var stored = [];
                try { stored = JSON.parse(localStorage.getItem('sqfm_cust_notif_stored') || '[]'); } catch (e) {}
                var freshMap = {};
                fresh.forEach(function (n) { freshMap[n.id] = n; });
                var dismissed = _dismissedStore();
                stored.forEach(function (s) { if (!freshMap[s.id] && dismissed.indexOf(s.id) === -1) fresh.push(s); });
                localStorage.setItem('sqfm_cust_notif_stored', JSON.stringify(
                    (data.notifications || []).map(function (n) { return { id:n.id, message:n.message, order_number:n.order_number, quantity:n.quantity, total:n.total, status:n.status, time:n.time, timestamp:n.timestamp, type:n.type, product_slug:n.product_slug }; })
                ));
                fresh.sort(function (a, b) { return (b.timestamp || 0) - (a.timestamp || 0); });
                var unreadCount = fresh.filter(function (n) { return readIds.indexOf(n.id) === -1; }).length;
                var prevUnread = parseInt(localStorage.getItem('sqfm_cust_unread_count') || '0', 10);
                if (unreadCount > prevUnread && prevUnread >= 0) playBell();
                localStorage.setItem('sqfm_cust_unread_count', unreadCount);
                if (unreadCount > 0) { badge.textContent = unreadCount > 99 ? '99+' : unreadCount; badge.style.display = 'flex'; }
                else { badge.style.display = 'none'; }
                if (fresh.length === 0) {
                    list.innerHTML = '<div style="padding:2rem;text-align:center;color:#a1887f;"><i class="ph ph-bell" style="font-size:2.5rem;margin-bottom:0.5rem;opacity:0.3;"></i><p style="font-size:0.9rem;margin:0;">No notifications</p></div>';
                    return;
                }
                list.innerHTML = fresh.map(function (n) { return _renderNotifItem(n, readIds); }).join('');
                list.querySelectorAll('.cust-mark-read').forEach(function (btn) {
                    btn.addEventListener('click', function (ev) { ev.stopPropagation(); markOneRead(btn.getAttribute('data-id')); });
                });
                list.querySelectorAll('.cust-notif-item').forEach(function (el) {
                    el.addEventListener('click', function () {
                        var id = el.getAttribute('data-id');
                        var read = _readStore();
                        if (read.indexOf(id) === -1) { read.push(id); _saveRead(read); }
                        var url = el.querySelector('a');
                        if (url) window.location.href = url.getAttribute('href');
                    });
                });
            } catch (e) {}
        }

        function clearNotif() {
            try {
                var items = document.querySelectorAll('.cust-notif-item');
                var dismissed = _dismissedStore();
                items.forEach(function (el) { var id = el.getAttribute('data-id'); if (dismissed.indexOf(id) === -1) dismissed.push(id); });
                _saveDismissed(dismissed);
                localStorage.removeItem('sqfm_cust_notif_read');
                localStorage.removeItem('sqfm_cust_notif_stored');
                localStorage.removeItem('sqfm_cust_unread_count');
                fetch('/api/customer/notifications/mark-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }).catch(function () {});
                loadNotif();
            } catch (e) {}
        }

        function _renderNotifItem(n, readIds) {
            var isRead = readIds.indexOf(n.id) !== -1;
            var clickUrl = n.type === 'chat' ? '{{ route('customer.chat') }}' : (n.type === 'review_reply' ? '{{ route('order') }}?review_reply=' + encodeURIComponent(n.product_slug || '') : '{{ route('customer.orders.all') }}');
            return '<div class="cust-notif-item" data-id="' + n.id + '" style="padding:1rem;border-bottom:1px solid #efebe9;cursor:pointer;transition:background 0.2s;background:' + (isRead ? 'transparent' : '#fff8ef') + ';" onmouseover="this.style.background=#fcfafa" onmouseout="this.style.background=' + (isRead ? 'transparent' : '#fff8ef') + '">' +
                '<div style="display:flex;align-items:flex-start;gap:0.75rem;">' +
                    (isRead ? '<span style="flex-shrink:0;color:#a1887f;padding-top:2px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>' : '<span style="flex-shrink:0;color:#6d4c41;padding-top:2px;"><i class="ph ph-bell"></i></span>') +
                    '<div style="flex:1;min-width:0;">' +
                        '<p style="font-size:0.85rem;font-weight:' + (isRead ? 400 : 600) + ';color:' + (isRead ? '#8d6e63' : '#4e342e') + ';margin:0 0 0.25rem 0;line-height:1.3;">' + n.message + '</p>' +
                        '<p style="font-size:0.8rem;color:#6d4c41;margin:0 0 0.25rem 0;">' + (n.order_number || '') + '</p>' +
                        (n.quantity ? '<p style="font-size:0.75rem;color:#8d6e63;margin:0;">Qty: ' + n.quantity + (n.total ? ' • Total: ' + n.total : '') + '</p>' : '') +
                        '<p style="font-size:0.7rem;color:#a1887f;margin:0.25rem 0 0 0;">' + n.time + (isRead ? ' · Read' : '') + '</p>' +
                    '</div>' +
                    (isRead ? '' : '<button class="cust-mark-read" data-id="' + n.id + '" title="Mark as read" style="flex-shrink:0;background:none;border:none;cursor:pointer;padding:4px;border-radius:6px;color:#8d6e63;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></button>') +
                '</div>' +
                '<div style="margin-top:0.5rem;"><a href="' + clickUrl + '" onclick="event.stopPropagation();" style="font-size:0.75rem;color:#6d4c41;text-decoration:underline;">View details →</a></div>' +
            '</div>';
        }

        function markOneRead(id) {
            var read = _readStore();
            if (read.indexOf(id) === -1) { read.push(id); _saveRead(read); }
            loadNotif();
        }

        function markAllRead() {
            var items = document.querySelectorAll('.cust-notif-item');
            var read = _readStore();
            items.forEach(function (el) { var id = el.getAttribute('data-id'); if (read.indexOf(id) === -1) read.push(id); });
            _saveRead(read);
            fetch('/api/customer/notifications/mark-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } }).catch(function () {});
            loadNotif();
        }
        // Initialize user dropdown
        function toggleUserDropdown(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('userDropdownMenu');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const userDropdown = document.getElementById('userDropdownMenu');
            const userContainer = document.getElementById('headerUserMenuContainer');
            if (userDropdown && userDropdown.style.display === 'block') {
                if (!userContainer.contains(e.target)) {
                    userDropdown.style.display = 'none';
                }
            }

            const notifDropdown = document.getElementById('notifDropdown');
            const notifContainer = document.getElementById('headerNotifContainer');
            if (notifDropdown && notifDropdown.style.display === 'block') {
                if (!notifContainer.contains(e.target)) {
                    notifDropdown.style.display = 'none';
                }
            }
        });

        // Load auto-refresh for notifs
        document.addEventListener('DOMContentLoaded', () => {
            loadNotif();
            setInterval(loadNotif, 30000);
        });
    </script>

    <div class="main-wrapper">
        <div class="main-container">
            <aside class="sidebar">
                <div class="sidebar-header" style="justify-content: center;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #4e342e; margin: 0; text-align: center;">Escalona's Farm</h2>
                </div>
                
                <nav class="sidebar-nav">
                    <div class="nav-section">
                        <div class="nav-title">My Account</div>
                        <a href="{{ route('customer.dashboard') }}" class="nav-item @if(View::yieldContent('active-nav') == 'dashboard') active @endif">
                            <i class="ph-bold ph-house"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('customer.profile') }}" class="nav-item @if(View::yieldContent('active-nav') == 'profile') active @endif">
                            <i class="ph-bold ph-user"></i>
                            <span>My Profile</span>
                        </a>
                    </div>
                    
                    <div class="nav-section">
                        <div class="nav-title">Shopping</div>
                        <a href="{{ route('order') }}" class="nav-item @if(View::yieldContent('active-nav') == 'browse') active @endif">
                            <i class="ph-bold ph-storefront"></i>
                            <span>Browse Products</span>
                        </a>
                    </div>
                    
                    <div class="nav-section">
                        <div class="nav-title">My Orders</div>
                        <a href="{{ route('customer.orders') }}" class="nav-item @if(View::yieldContent('active-nav') == 'orders') active @endif">
                            <i class="ph-bold ph-package"></i>
                            <span>All Orders</span>
                        </a>
                    </div>
                    
                    <div class="nav-section">
                        <div class="nav-title">Support</div>
                        <a href="{{ route('customer.chat') }}" class="nav-item @if(View::yieldContent('active-nav') == 'chat') active @endif" onclick="notifyNavigation('Chat with Us', '💬')">
                            <i class="ph-bold ph-chat-circle-text"></i>
                            <span>Chat with Us</span>
                        </a>
                        <form method="POST" action="{{ route('customer.logout') }}" style="display: contents;">
                            @csrf
                            <button type="submit" class="nav-item" style="background: none; border: none; width: 100%; text-align: left; cursor: pointer;">
                                <i class="ph-bold ph-sign-out"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </nav>
            </aside>

            <main class="main-content">
                <div class="content-header">
                    <h1 class="content-title">@yield('page-title')</h1>
                    @yield('header-action')
                </div>
                
                @yield('customer-content')
            </main>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-inner">
            <div>
                <div class="footer-brand-name">Escalona's Farm</div>
                <div class="footer-brand-desc">Our Intelligent Quail Management System (SQUIFM) automatically monitors conditions ensures that our birds live in the optimal environment, resulting in healthier livestock and vastly superior products.</div>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Quick Links</div>
                <div class="footer-links">
                    <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                    <a href="{{ route('customer.profile') }}">My Profile</a>
                    <a href="{{ route('order') }}">Browse Products</a>
                    <a href="{{ route('customer.orders') }}">All Orders</a>
                </div>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Account</div>
                <div class="footer-links">
                    <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                    <a href="{{ route('customer.profile') }}">My Profile</a>
                    <a href="{{ route('customer.orders') }}">All Orders</a>
                </div>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Contact Info</div>
                <ul class="footer-contact">
                    <li><i class="ph-fill ph-map-pin"></i> Pagkakaisa, Naujan, Or. Mindoro</li>
                    <li><i class="ph-fill ph-phone"></i> +63 917 123 4567</li>
                    <li><i class="ph-fill ph-envelope-simple"></i> escalona.farm@gmail.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">&copy; {{ date('Y') }} Escalona's Farm | Capstone SQUIFM. All rights reserved.</div>
    </footer>
</body>
</html>
