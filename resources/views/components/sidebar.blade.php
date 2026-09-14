@php
    $current = request()->route()->getName();
    // Floating notification bell: show only on pages where notifications are managed
    // (Dashboard, Orders & Products, Inventory & Sales, Customer Chat, Admin Reviews,
    //  Feeder + Feeder Schedule, Temperature & Humidity, Quail Detection, Learn Book, Settings)
    $showNotificationBell = in_array($current, [
        'dashboard',
        'orders-products',
        'inventory',
        'admin.chat',
        'admin.reviews',
        'feeder',
        'feeder.schedule',
        'temperature.humidity',
        'quail-detection',
        'feeder.learnbook',
        'settings',
    ]);
@endphp

<style>
/* ── Sidebar ── */
#sidebar {
    position: fixed;
    top: 72px; left: 0;
    height: calc(100vh - 72px);
    width: 300px;
    background: linear-gradient(180deg, #fffdfa 0%, #f5eee6 100%);
    border-right: 2px solid #d7ccc8;
    box-shadow: 4px 0 20px rgba(121,85,72,0.08);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Icon-only collapsed state (YouTube style) */
/* Icon-only collapsed state */
#sidebar.icon-only {
    width: 80px;
}

/* Fully hidden collapsed state (for mobile toggle) */
#sidebar.collapsed { transform: translateX(-300px); }

/* Toggle button - inside sidebar header */
#sidebar-toggle {
    width: 36px; height: 36px;
    border-radius: 8px;
    border: 2px solid #d7ccc8;
    background: #fffdfa;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 1001;
}
#sidebar-toggle:hover { background: #efebe9; border-color: #a1887f; }

/* Hamburger button inside the topbar header */
#sidebar-hamburger {
    display: none;
    align-items: center;
    gap: 0.5rem;
}

/* Notification bell - now inside the dashboard topbar */
#floating-notification-bell {
    position: relative;
    display: flex;
    z-index: 1000;
}
.hb-btn {
    width: 40px; height: 40px;
    border-radius: 10px;
    border: 2px solid #a1887f;
    background: #fffdfa;
    box-shadow: 0 2px 8px rgba(161,136,127,0.2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
    position: relative;
}
    background: #fffdfa;
    box-shadow: 0 2px 8px rgba(161,136,127,0.2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
    position: relative;
}
 .hb-btn:hover { background: #f0ebe6; border-color: #6d4c41; }
 
 /* ── Dashboard Top Header Bar ── */
 .dashboard-topbar {
     position: sticky;
     top: 0;
     z-index: 900;
     display: flex;
     align-items: center;
     justify-content: space-between;
     gap: 1rem;
     padding: 1rem 2rem;
     height: 72px;
     min-height: 72px;
     width: 100%;
     background: rgba(255, 253, 250, 0.92);
     backdrop-filter: blur(12px);
     -webkit-backdrop-filter: blur(12px);
     border-bottom: 2px solid rgba(215, 204, 200, 0.6);
     box-shadow: 0 2px 12px rgba(121, 85, 72, 0.08);
 }
 .topbar-left {
     display: flex;
     align-items: center;
     gap: 1rem;
     min-width: 0;
     flex: 1;
 }
 .topbar-date {
     color: #8d6e63;
     font-size: 1.05rem;
     font-weight: 600;
     letter-spacing: 0.3px;
     white-space: nowrap;
 }
 .topbar-right {
     display: flex;
     align-items: center;
     gap: 1rem;
     flex-shrink: 0;
 }
 .topbar-user {
     display: flex;
     align-items: center;
     gap: 0.75rem;
 }
 .topbar-avatar {
     width: 44px;
     height: 44px;
     border-radius: 50%;
     background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
     color: #fffdfa;
     font-weight: 800;
     font-size: 1.1rem;
     display: flex;
     align-items: center;
     justify-content: center;
     box-shadow: 0 3px 10px rgba(109, 76, 65, 0.25);
     border: 2px solid #d7ccc8;
     flex-shrink: 0;
 }
 .topbar-name {
     font-size: 1.1rem;
     font-weight: 800;
     color: #6d4c41;
     white-space: nowrap;
     max-width: 240px;
     overflow: hidden;
     text-overflow: ellipsis;
 }
 
 @media (max-width: 768px) {
     .dashboard-topbar {
         padding: 0.7rem 1.2rem;
         height: 64px;
         min-height: 64px;
     }
     .topbar-date { font-size: 0.85rem; }
     .topbar-name { font-size: 0.9rem; max-width: 110px; }
     .topbar-avatar { width: 36px; height: 36px; font-size: 0.9rem; }
 }


/* Notification bell in hamburger */
.notification-bell {
    width: 40px; height: 40px;
    border-radius: 10px;
    border: 2px solid #a1887f;
    background: #fffdfa;
    box-shadow: 0 2px 8px rgba(161,136,127,0.2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease;
    position: relative;
}
.notification-bell:hover { 
    background: #f0ebe6; 
    border-color: #6d4c41; 
}
.notification-bell .bell-icon {
    animation: bellRing 3s ease-in-out infinite;
}
@keyframes bellRing {
    0%, 80%, 100% { transform: rotate(0deg); }
    83%  { transform: rotate(-12deg); }
    87%  { transform: rotate(12deg); }
    91%  { transform: rotate(-8deg); }
    95%  { transform: rotate(6deg); }
    98%  { transform: rotate(0deg); }
}

/* Notification dropdown positioning */
.notification-dropdown {
    position: fixed;
    top: 82px;
    right: 2rem;
    left: auto;
    width: 320px;
    max-height: 450px;
    overflow-y: auto;
    background: linear-gradient(135deg, #fffdfa 0%, #f5f0eb 100%);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(109,76,65,0.2);
    border: 2px solid #efebe9;
    z-index: 999;
    display: none;
}
.notification-dropdown.show {
    display: block;
    animation: slideDown 0.3s ease;
}
@keyframes slideDown {
    from { 
        opacity: 0; 
        transform: translateY(-10px) scale(0.95);
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1);
    }
}

/* Sidebar header */
.sidebar-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 3.5rem 1.25rem 1.25rem;
    border-bottom: 2px solid #efebe9;
    position: relative;
}

.sidebar-logo {
    width: 80px; height: 80px;
    border-radius: 12px;
    border: 2px solid #a1887f;
    overflow: hidden;
    flex-shrink: 0;
}

.sidebar-logo img { width: 100%; height: 100%; object-fit: cover; }
.sidebar-brand {
    font-size: 1rem;
    font-weight: 800;
    color: #6d4c41;
    letter-spacing: 0.3px;
    line-height: 1.3;
    text-align: center;
}

.sidebar-scientific-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: #8d6e63;
    letter-spacing: 0.2px;
    font-style: italic;
    text-align: center;
}

/* Nav items */
.sidebar-nav {
    flex: 1;
    padding: 1rem 0.75rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    color: #6d4c41;
    text-decoration: none;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: 2px solid #a1887f;
    background: #fffdfa;
    box-shadow: 0 4px 12px rgba(141,110,99,0.10);
    cursor: pointer;
    width: 100%;
    text-align: left;
}

.sidebar-item:hover {
    background: #f8ede3;
    box-shadow: 0 6px 16px rgba(141,110,99,0.18);
}

.sidebar-item.active {
    background: #a1887f;
    color: #fffdfa;
    border-color: #a1887f;
    box-shadow: 0 6px 16px rgba(141,110,99,0.18), 0 0 20px rgba(161,136,127,0.5);
}

.sidebar-item.active svg { stroke: #fffdfa; }

.sidebar-divider {
    height: 1px;
    background: #efebe9;
    margin: 0.5rem 0.75rem;
}

/* Sidebar footer */
.sidebar-footer {
    padding: 0.75rem;
    border-top: 2px solid #efebe9;
}

/* Main content offset */
.sidebar-content-wrap {
    margin-left: 300px;
    padding-top: 72px;
    transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding-top 0.3s ease;
}
.sidebar-content-wrap.expanded { margin-left: 0; }

/* Overlay for mobile */
#sidebar-overlay {
    display: none;
    position: fixed;
    inset: 72px 0 0 0;
    background: rgba(0,0,0,0.3);
    z-index: 999;
}
#sidebar-overlay.show { display: block; }

@media (max-width: 768px) {
    #sidebar { 
        transform: translateX(-300px); 
        top: 64px;
        height: calc(100vh - 64px);
    }
    #sidebar.collapsed { transform: translateX(-300px); }
    #sidebar.mobile-open { transform: translateX(0); }
    .sidebar-content-wrap { margin-left: 0 !important; padding-top: 64px; }
    #sidebar-overlay {
        inset: 64px 0 0 0;
    }
}

/* Logout Modal Styles */
.confirm-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.confirm-modal-overlay.show {
    display: flex;
}
.confirm-modal {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.confirm-modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #4e342e;
    margin-bottom: 1rem;
}
.confirm-modal-message {
    color: #6d4c41;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}
.confirm-modal-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}
.confirm-modal-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.confirm-modal-btn.cancel {
    background: #e0e0e0;
    color: #666;
}
.confirm-modal-btn.cancel:hover {
    background: #d0d0d0;
}
.confirm-modal-btn.confirm {
    background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
    color: white;
}
.confirm-modal-btn.confirm:hover {
    box-shadow: 0 4px 12px rgba(109, 76, 65, 0.3);
}
</style>

<!-- Dashboard Top Header -->
<div id="dashboard-topbar" class="dashboard-topbar">
    <div class="topbar-left">
        <div id="sidebar-hamburger" style="display: none;">
            <button class="hb-btn" onclick="toggleSidebar()" title="Open menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
        <span class="topbar-date" id="topbar-date"></span>
    </div>
    <div class="topbar-right">
        @if($showNotificationBell)
        <div id="floating-notification-bell">
            <div class="notification-bell" id="notification-bell" title="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="1.8" class="bell-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span id="notification-badge" style="position:absolute;top:-6px;right:-6px;background:linear-gradient(135deg,#ff6b6b,#ff5252);color:white;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;align-items:center;justify-content:center;display:none;">0</span>
            </div>
        </div>
        @endif
        <div class="topbar-user">
            <div class="topbar-avatar">{{ strtoupper(substr(trim(auth()->user()->name ?? 'U'), 0, 1)) }}</div>
            <span class="topbar-name">{{ auth()->user()->name ?? 'User' }}</span>
        </div>
    </div>
</div>

<!-- Notification Dropdown -->
<div id="notification-dropdown" class="notification-dropdown" style="position:fixed;top:60px;right:1rem;left:auto;width:320px;max-height:450px;background:linear-gradient(135deg,#fffdfa,#f5f0eb);border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:999;overflow:hidden;flex-direction:column;">
    <div style="padding:16px;border-bottom:1px solid #e0d7d0;font-weight:600;color:#6d4c41;font-size:14px;display:flex;justify-content:space-between;align-items:center;">
        <span>Notifications</span>
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <button id="mark-all-read-btn" style="background:none;border:none;color:#6d4c41;cursor:pointer;font-size:12px;text-decoration:underline;font-weight:600;">Mark all read</button>
            <button id="clear-all-btn" style="background:none;border:none;color:#a1887f;cursor:pointer;font-size:12px;text-decoration:underline;">Clear All</button>
        </div>
    </div>
    <div id="notification-list" style="flex:1;overflow-y:auto;padding:0;">
        <div style="padding:32px 16px;text-align:center;color:#a1887f;font-size:13px;">
            No notifications
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div id="sidebar">
    <!-- Close/collapse button inside header -->
    <button id="sidebar-toggle" onclick="toggleSidebar()" title="Close menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
        </div>
        <span class="sidebar-brand">Escalona's Quail Farm</span>
        <span class="sidebar-scientific-name">
            @if(isset($currentBreeds) && count($currentBreeds) > 0)
                @php
                    // Get unique breeds to avoid showing duplicates
                    $uniqueBreeds = [];
                    $breedIds = [];
                    foreach ($currentBreeds as $breed) {
                        if (!in_array($breed->id, $breedIds)) {
                            $uniqueBreeds[] = $breed;
                            $breedIds[] = $breed->id;
                        }
                    }
                @endphp
                @if(count($uniqueBreeds) > 1)
                    @foreach($uniqueBreeds as $index => $breed)
                        {{ $breed->name }}@if($index < count($uniqueBreeds) - 1) & @endif
                    @endforeach
                @else
                    {{ $uniqueBreeds[0]->name }}
                @endif
            @elseif($currentQuailBreed)
                {{ $currentQuailBreed->name }}
            @else
                Japanese Coturnix Crossbreed (Taiwan Brown Line)
            @endif
        </span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ $current === 'dashboard' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{ __('common.dashboard') ?? 'Dashboard' }}
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('orders-products') }}" class="sidebar-item {{ $current === 'orders-products' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Orders & Products
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('inventory') }}" class="sidebar-item {{ $current === 'inventory' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m3.3 7 8.7 5 8.7-5" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12" />
            </svg>
            {{ __('common.inventory_sales') }}
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('admin.chat') }}" class="sidebar-item {{ $current === 'admin.chat' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Customer Chat
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('quail-detection') }}" class="sidebar-item {{ $current === 'quail-detection' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ __('common.quail_detection') ?? 'Quail Detection' }}
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('feeder.learnbook') }}" class="sidebar-item {{ $current === 'feeder.learnbook' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
            </svg>
            {{ __('common.learn_book') ?? 'Learn Book' }}
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('feeder') }}" class="sidebar-item {{ in_array($current, ['feeder','feeder.schedule']) ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 12h8m-8 6h8" />
                <circle cx="12" cy="12" r="1" />
            </svg>
            {{ __('common.feeder') }}
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('temperature.humidity') }}" class="sidebar-item {{ $current === 'temperature.humidity' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0z" />
            </svg>
            {{ __('common.temperature_humidity') }}
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('settings') }}" class="sidebar-item {{ $current === 'settings' ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            {{ __('common.settings') }}
        </a>
    </nav>

    <div class="sidebar-footer">
        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" id="logoutForm" style="display:contents;">
            @csrf
            <button type="button" class="sidebar-item" onclick="showLogoutModal()" style="width:100%;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                {{ __('common.logout') }}
            </button>
        </form>
    </div>
</div>

<!-- Logout Modal -->
<div class="confirm-modal-overlay" id="logoutModal">
    <div class="confirm-modal">
        <div class="confirm-modal-title">Confirm Logout</div>
        <div class="confirm-modal-message">Are you sure you want to logout from your account?</div>
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn cancel" onclick="closeLogoutModal()">Cancel</button>
            <button class="confirm-modal-btn confirm" onclick="submitLogout()">Yes, Logout</button>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar   = document.getElementById('sidebar');
    const wrap      = document.getElementById('main-content-wrap');
    const overlay   = document.getElementById('sidebar-overlay');
    const hamburger = document.getElementById('sidebar-hamburger');
    
    if (window.innerWidth <= 768) {
        // Mobile behavior
        const isMobileOpen = sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('show', isMobileOpen);
        hamburger.style.display = 'flex';
    } else {
        // Desktop behavior
        const collapsed = sidebar.classList.toggle('collapsed');
        if (wrap) wrap.classList.toggle('expanded', collapsed);
        hamburger.style.display = collapsed ? 'flex' : 'none';
        localStorage.setItem('sidebar_collapsed', collapsed ? '1' : '0');
        const notifBell = document.getElementById('floating-notification-bell');
        if (notifBell) notifBell.style.display = 'flex';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const sidebar   = document.getElementById('sidebar');
    const wrap      = document.getElementById('main-content-wrap');
    const overlay   = document.getElementById('sidebar-overlay');
    const hamburger = document.getElementById('sidebar-hamburger');

    // Mobile: Always start with sidebar hidden
    const notifBell = document.getElementById('floating-notification-bell');
    const topbarDate = document.getElementById('topbar-date');
    
    // Topbar date
    if (topbarDate) {
        topbarDate.textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    if (window.innerWidth <= 768) {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
        hamburger.style.display = 'flex';
        if (notifBell) notifBell.style.display = 'flex';
        if (wrap) wrap.classList.remove('expanded');
    } else {
        // Desktop: Check localStorage for collapsed state
        if (localStorage.getItem('sidebar_collapsed') === '1') {
            sidebar.classList.add('collapsed');
            if (wrap) wrap.classList.add('expanded');
            hamburger.style.display = 'flex';
        } else {
            hamburger.style.display = 'none';
        }
        if (notifBell) notifBell.style.display = 'flex';
    }

    // Close sidebar on nav item click (mobile)
    document.querySelectorAll('.sidebar-item').forEach(function(item) {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            }
        });
    });
});

// Logout modal functions
function showLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.classList.add('show');
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.classList.remove('show');
}

function submitLogout() {
    const form = document.getElementById('logoutForm');
    if (form) form.submit();
}

// ── Unified Notification Bell Feed ──────────────────────────────────────
// Aggregates Orders & Products, Inventory, Customer Chat, Feeder and
// Temperature & Humidity notifications from /api/admin/notification-feed.
window.__squifmBellFeedActive = true; // legacy page scripts must leave #notification-list alone

const SquifmBell = {
    items: [],
    acks: [],
    dismissed: [],
    open: false,
    audioCtx: null,
    lastUnreadKeys: null, // null = first load (don't chime on page refresh)
    retentionMs: 5 * 60 * 60 * 1000,

    // ── Dismissed (Cleared) notifications are persisted locally so they do
    //    NOT come back on the next poll / page refresh. ──
    dismissedKey() { return 'sqfm_bell_dismissed'; },

    loadDismissed() {
        try {
            const d = JSON.parse(localStorage.getItem(this.dismissedKey()) || '[]');
            this.dismissed = Array.isArray(d) ? d : [];
        } catch (e) { this.dismissed = []; }
    },

    saveDismissed() {
        try { localStorage.setItem(this.dismissedKey(), JSON.stringify(this.dismissed)); } catch (e) {}
    },

    cachedItemsKey() { return 'sqfm_bell_items'; },

    loadCachedItems() {
        try {
            const cached = JSON.parse(localStorage.getItem(this.cachedItemsKey()) || '[]');
            if (!Array.isArray(cached)) return [];

            const now = Date.now();
            const active = cached.filter(entry =>
                entry && entry.item && entry.item.key && entry.expiresAt > now
            );
            localStorage.setItem(this.cachedItemsKey(), JSON.stringify(active));
            return active;
        } catch (e) {
            return [];
        }
    },

    saveCachedItems(items) {
        try {
            localStorage.setItem(this.cachedItemsKey(), JSON.stringify(items));
        } catch (e) {}
    },

    retainItems(items) {
        const now = Date.now();
        const cached = this.loadCachedItems();
        const byKey = new Map(cached.map(entry => [entry.item.key, entry]));

        items.forEach(item => {
            const timestamp = (item.ts || item._ts) ? (item.ts || item._ts) * 1000 : now;
            byKey.set(item.key, {
                item,
                expiresAt: timestamp + this.retentionMs
            });
        });

        const active = Array.from(byKey.values()).filter(entry => entry.expiresAt > now);
        this.saveCachedItems(active);
        return active.map(entry => entry.item);
    },

    csrf() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    },

    // Create/resume the WebAudio context (browsers require a user gesture first)
    ensureAudio() {
        try {
            if (!this.audioCtx) {
                const AC = window.AudioContext || window.webkitAudioContext;
                if (!AC) return null;
                this.audioCtx = new AC();
            }
            if (this.audioCtx.state === 'suspended') this.audioCtx.resume();
            return this.audioCtx;
        } catch (e) {
            return null;
        }
    },

    // Two-tone chime (E6 → G6) generated with WebAudio — no audio file needed
    playChime() {
        const ctx = this.ensureAudio();
        if (!ctx) return;
        try {
            [[1318.5, 0], [1568.0, 0.18]].forEach(([freq, delay]) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                osc.connect(gain);
                gain.connect(ctx.destination);
                const t0 = ctx.currentTime + delay;
                gain.gain.setValueAtTime(0.0001, t0);
                gain.gain.exponentialRampToValueAtTime(0.22, t0 + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, t0 + 0.5);
                osc.start(t0);
                osc.stop(t0 + 0.55);
            });
        } catch (e) { /* never break the feed because of sound */ }
    },

    async loadAcks() {
        this.loadDismissed();
        try {
            const r = await fetch('/api/admin/notification-acks', { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            this.acks = Array.isArray(d.keys) ? d.keys : [];
        } catch (e) { this.acks = []; }
    },

    async loadFeed() {
        this.items = this.loadCachedItems()
            .map(entry => entry.item)
            .filter(i => !this.dismissed.includes(i.key));
        this.render();

        try {
            const r = await fetch('/api/admin/notification-feed', { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            if (Array.isArray(d.items)) {
                // Keep each notification in the bell for five hours, even after
                // it is read or no longer matches the live unread query.
                const retained = this.retainItems(d.items);
                this.items = retained.filter(i => !this.dismissed.includes(i.key));
            }
        } catch (e) {
            // Network hiccup — keep the current feed and its retained items.
        }

        // Chime only when brand-new unread notifications arrived since last poll
        const unreadKeys = this.items.filter(i => !this.acks.includes(i.key)).map(i => i.key);
        if (this.lastUnreadKeys !== null) {
            const fresh = unreadKeys.filter(k => !this.lastUnreadKeys.includes(k));
            if (fresh.length > 0) this.playChime();
        }
        this.lastUnreadKeys = unreadKeys;

        this.render();
    },

    unreadCount() {
        return this.items.filter(i => !this.acks.includes(i.key)).length;
    },

    // Human-friendly section label for a unix timestamp (seconds)
    dateLabel(ts) {
        if (!ts) return 'Earlier';
        const ms = ts * 1000;
        const now = new Date();
        const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
        const dayMs = 86400000;
        if (ms >= startOfToday) return 'Today';
        if (ms >= startOfToday - dayMs) return 'Yesterday';
        return new Date(ms).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    },

    // Professional SVG icons per notification module/category —
    // replaces every emoji coming from the feed so the bell dropdown
    // never shows emoji or "diamond ?" glyphs.
    svgIconFor(category) {
        const icons = {
            // Orders & Products → shopping cart
            order: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>',
            // Reviews → star
            review: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
            // Customer Chat → message square
            chat: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
            // Feeder → utensils (feeding)
            feeder: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>',
            // Temperature & Humidity → thermometer
            climate: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"></path></svg>',
            // Inventory & Sales → package/box
            inventory: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>',
            // Quail Detection → camera/scan
            detection: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
            // Learn Book → book
            learn: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
            // Settings → gear
            settings: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
        };
        // Default → bell
        return icons[category] || '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
    },

    itemHtml(i) {
        const unread = !this.acks.includes(i.key);
        const dismissed = this.dismissed.includes(i.key);
        const timeText = i.ts
            ? new Date(i.ts * 1000).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })
            : (i.time || '');
        // Show "Mark as read" for unread, and a "Read" check for already-read items
        const actionBtn = unread
            ? `<button class="squifm-mark-read" data-key="${i.key}" title="Mark as read" style="flex-shrink:0;background:none;border:none;cursor:pointer;padding:6px;border-radius:8px;color:#8d6e63;display:flex;align-items:center;justify-content:center;transition:all .2s ease;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
               </button>`
            : `<span title="Read" style="flex-shrink:0;display:flex;align-items:center;justify-content:center;padding:6px;color:#a1887f;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
               </span>`;
        return `
                <div class="squifm-bell-item" data-key="${i.key}" data-url="${i.url || ''}" style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 1rem;border-bottom:1px solid #f3ece5;background:${unread ? '#fff8ef' : 'transparent'};cursor:pointer;opacity:${dismissed ? 0.5 : 1};">
                    <span style="font-size:1.25rem;line-height:1.4;color:${unread ? '#6d4c41' : '#bd9c8f'};flex-shrink:0;">${this.svgIconFor(i.category)}</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.85rem;font-weight:${unread ? 700 : 400};color:${unread ? '#4e342e' : '#8d6e63'};">${i.title || 'Notification'}${unread ? '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#ff5252;margin-left:4px;vertical-align:middle;"></span>' : ''}</div>
                        <div style="font-size:0.78rem;color:${unread ? '#8d6e63' : '#bd9c8f'};">${i.message || ''}</div>
                        <div style="font-size:0.7rem;color:#bd9c8f;margin-top:2px;">${timeText}${unread ? '' : ' · Read'}</div>
                    </div>
                    ${actionBtn}
                </div>`;
    },

    render() {
        const list = document.getElementById('notification-list');
        const badge = document.getElementById('notification-badge');
        if (!list || !badge) return;

        if (this.items.length === 0) {
            list.innerHTML = '<div style="padding:32px 16px;text-align:center;color:#a1887f;font-size:13px;">No notifications</div>';
        } else {
            // Organized: newest first, grouped into date sections (Today / Yesterday / dates)
            const sorted = [...this.items].sort((a, b) => (b.ts || 0) - (a.ts || 0));
            const groups = [];
            sorted.forEach(i => {
                const label = this.dateLabel(i.ts || 0);
                const last = groups[groups.length - 1];
                if (last && last.label === label) {
                    last.items.push(i);
                } else {
                    groups.push({ label, items: [i] });
                }
            });

            list.innerHTML = groups.map(g => `
                <div style="position:sticky;top:0;padding:7px 1rem 5px;font-size:0.68rem;font-weight:800;color:#9c7b6d;text-transform:uppercase;letter-spacing:0.6px;background:#f7f1ea;border-bottom:1px solid #efe7e0;z-index:1;">${g.label} · ${g.items.length}</div>
                ${g.items.map(i => this.itemHtml(i)).join('')}
            `).join('');

            list.querySelectorAll('.squifm-bell-item').forEach(el => {
                el.addEventListener('click', () => {
                    this.ackKeys([el.dataset.key]);
                    if (el.dataset.url) window.location.href = el.dataset.url;
                });
            });
            // "Mark as read" per item (must not trigger navigation)
            list.querySelectorAll('.squifm-mark-read').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.markRead(btn.dataset.key);
                });
            });
        }

        const unread = this.unreadCount();
        if (unread > 0) {
            badge.textContent = unread > 99 ? '99+' : unread;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    },

    async ackKeys(keys) {
        if (!keys || !keys.length) return;
        keys.forEach(k => { if (!this.acks.includes(k)) this.acks.push(k); });
        this.render();
        try {
            await fetch('/api/admin/notification-acks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf(), 'Accept': 'application/json' },
                body: JSON.stringify({ keys })
            });
        } catch (e) { /* offline — stays acknowledged locally for this session */ }
    },

    toggle(force) {
        const dropdown = document.getElementById('notification-dropdown');
        if (!dropdown) return;
        this.open = typeof force === 'boolean' ? force : !this.open;
        dropdown.classList.toggle('show', this.open);
        // Keep unread items highlighted until the user explicitly marks them
        // read or clears them, so the per-item "Mark read" button stays useful.
    },

    // Mark every visible notification as read (keeps them in the list)
    markAllRead() {
        this.ackKeys(this.items.map(i => i.key));
    },

    clearAll() {
        // Persist dismissed keys so they don't reappear after the next poll / refresh
        this.items.forEach(i => { if (!this.dismissed.includes(i.key)) this.dismissed.push(i.key); });
        this.saveDismissed();
        this.saveCachedItems([]);
        // Acknowledge all items (marks as read on the server too)
        this.ackKeys(this.items.map(i => i.key));
        // Clear the items list so they don't show anymore
        this.items = [];
        // Re-render to show empty state
        this.render();
    },

    // Mark a single notification as read (keeps it visible, removes unread highlight)
    markRead(key) {
        this.ackKeys([key]);
        // Re-render so the unread highlight / dot disappears
        this.render();
    }
};

// Bind interactions via listeners so page-level script overrides can't break the bell
const squifmBellEl = document.getElementById('notification-bell');
if (squifmBellEl) {
    squifmBellEl.addEventListener('click', function (e) {
        e.stopPropagation();
        SquifmBell.toggle();
    });
}
document.getElementById('clear-all-btn')?.addEventListener('click', function (e) {
    e.stopPropagation();
    SquifmBell.clearAll();
});
document.getElementById('mark-all-read-btn')?.addEventListener('click', function (e) {
    e.stopPropagation();
    SquifmBell.markAllRead();
});
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('notification-dropdown');
    const bell = document.getElementById('notification-bell');
    if (SquifmBell.open && dropdown && !dropdown.contains(e.target) && !(bell && bell.contains(e.target))) {
        SquifmBell.toggle(false);
    }
});

// Initial load + poll every 10 seconds (only when the bell exists on this page)
if (document.getElementById('floating-notification-bell')) {
    SquifmBell.loadAcks().then(() => SquifmBell.loadFeed());
    setInterval(() => SquifmBell.loadFeed(), 10000);
}

// Unlock audio on the first user interaction (browser autoplay policy)
// so the chime can play later even without a fresh click
['click', 'keydown'].forEach(function (evt) {
    document.addEventListener(evt, function audioUnlock() {
        if (typeof SquifmBell !== 'undefined') SquifmBell.ensureAudio();
        document.removeEventListener(evt, audioUnlock);
    }, { once: true });
});
</script>
