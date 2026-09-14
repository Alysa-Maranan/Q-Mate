@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Navigation Bar */
    .nav-bar {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        border-bottom: 2px solid #d7ccc8;
        box-shadow: 0 4px 20px rgba(121, 85, 72, 0.08);
        padding: 1.25rem 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .logo-section {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .logo {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(121, 85, 72, 0.18);
        border: 2px solid #a1887f;
        overflow: hidden;
    }
    .logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .logo-text {
        color: #6d4c41;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    .nav-menu {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .nav-item {
        color: #6d4c41;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: #fffdfa;
        border: 2px solid #a1887f;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.10);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .nav-item:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18);
    }
    .nav-item.active {
        background: #a1887f;
        color: #fffdfa;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5);
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    @keyframes ripple-animation {
        to { transform: scale(4); opacity: 0; }
    }

    .container {
        max-width: 100%;
        margin: 0 auto;
        padding: 2.5rem 3rem;
    }

    .page-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
    }

    /* Food Tank */
    .food-level-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2.5rem;
        align-items: start;
    }

    .tank-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
    }

    .food-tank-top {
        width: 160px;
        height: 16px;
        background: #9ca3af;
        border-radius: 6px 6px 0 0;
        margin-bottom: -2px;
    }

    .food-tank {
        width: 140px;
        height: 260px;
        border: 4px solid #d1d5db;
        border-radius: 0 0 20px 20px;
        position: relative;
        overflow: hidden;
        background: #f3f4f6;
    }

    .food-tank-fill {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        transition: height 0.8s cubic-bezier(0.4, 0, 0.2, 1), background 0.5s;
        border-radius: 0 0 16px 16px;
    }
    .food-tank-fill.level-normal {
        background: linear-gradient(180deg, #6ee7b7 0%, #10b981 100%);
    }
    .food-tank-fill.level-low {
        background: linear-gradient(180deg, #fcd34d 0%, #f59e0b 100%);
    }
    .food-tank-fill.level-empty {
        background: linear-gradient(180deg, #fca5a5 0%, #ef4444 100%);
    }

    .food-tank-label {
        position: absolute;
        width: 100%;
        text-align: center;
        font-weight: 800;
        font-size: 1.5rem;
        color: white;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        bottom: 10px;
    }

    .tank-status-text {
        font-weight: 700;
        font-size: 1.15rem;
        color: #065f46;
        text-align: center;
    }

    /* Animated progress bar in header card */
    .level-bar-wrapper {
        width: 100%;
        height: 32px;
        background: #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        border: 3px solid #d1d5db;
        position: relative;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    .level-bar-fill {
        height: 100%;
        border-radius: 13px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }
    .level-bar-fill.normal { background: linear-gradient(90deg, #10b981 0%, #34d399 50%, #6ee7b7 100%); }
    .level-bar-fill.low    { background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 50%, #fcd34d 100%); }
    .level-bar-fill.empty  { background: linear-gradient(90deg, #ef4444 0%, #f87171 50%, #fca5a5 100%); }

    .level-bar-shine {
        position: absolute;
        top: 0; left: -100%;
        width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.45), transparent);
        animation: shine 2.5s infinite;
    }
    @keyframes shine {
        0%   { left: -100%; }
        100% { left: 200%; }
    }

    /* Details card */
    .detail-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .detail-section {
        background: #f0fdf4;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #d1fae5;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid #d1fae5;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #6b7280; }
    .detail-value { font-weight: 700; color: #065f46; }

    .legend-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid #e5e7eb;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
        font-size: 0.875rem;
    }
    .legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Alert banner */
    .alert-banner {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        margin-top: 1.5rem;
        display: none;
    }
    .alert-banner.warning {
        background: #fef3c7;
        border: 2px solid #f59e0b;
        color: #92400e;
        display: block;
    }
    .alert-banner.danger {
        background: #fee2e2;
        border: 2px solid #ef4444;
        color: #991b1b;
        display: block;
    }
    .alert-banner.ok {
        background: #d1fae5;
        border: 2px solid #10b981;
        color: #065f46;
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 🔔 Notification Bell Styles - Brown Theme */
    .notification-bell {
        position: relative;
        cursor: pointer;
        padding: 0.5rem;
        font-size: 1.5rem;
        background: white;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #a1887f;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.2);
        transition: all 0.3s ease;
    }
    .notification-bell:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.3);
        transform: scale(1.1);
    }
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }
    .notification-badge.has-notifications {
        animation: pulse-badge 2s infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    .notification-dropdown {
        position: absolute;
        top: 60px;
        right: 0;
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid #d7ccc8;
        z-index: 1001;
        display: none;
    }
    .notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .notification-header {
        padding: 1rem;
        border-bottom: 1px solid #d7ccc8;
        font-weight: 700;
        color: #6d4c41;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .notification-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #efebe9;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        transition: background 0.2s ease;
    }
    .notification-item:hover { background: #efebe9; }
    .notification-item:last-child { border-bottom: none; }
    .notification-item.success { border-left: 4px solid #a1887f; }
    .notification-item.info { border-left: 4px solid #8d6e63; }
    .notification-icon { font-size: 1.25rem; }
    .notification-content { flex: 1; }
    .notification-text { font-size: 0.875rem; color: #1f2937; }
    .notification-time { font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem; }
    .notification-empty { padding: 2rem; text-align: center; color: #6b7280; }
    .clear-notifications { font-size: 0.75rem; color: #ef4444; cursor: pointer; text-decoration: underline; }
</style>

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding-top:2.5rem;">
    <!-- Page Header with quick bar -->
    <div class="page-header">
        <h1 class="page-title">📊 Food Level Monitor</h1>
        <p style="color: #6b7280; margin: 0.5rem 0 1.25rem;">Real-time food storage status from the ESP32 sensor.</p>

        <div class="level-bar-wrapper">
            <div class="level-bar-fill normal" id="header-bar-fill" style="width: {{ \App\Models\FoodLevel::getCurrent()->level }}%;">
                <div class="level-bar-shine"></div>
                <span id="header-bar-text"></span>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="food-level-grid">
        <!-- Left: Tank Graphic -->
        <div class="tank-card">
            <h3 style="margin: 0; color: #065f46; font-size: 1.15rem;">🏗️ Storage Tank</h3>
            <div class="food-tank-top"></div>
            <div class="food-tank" id="food-tank">
                @php $fl = \App\Models\FoodLevel::getCurrent(); @endphp
                <div class="food-tank-fill level-{{ $fl->status === 'normal' ? 'normal' : ($fl->status === 'low' ? 'low' : 'empty') }}" id="food-tank-fill" style="height: {{ $fl->level }}%;">
                    <span class="food-tank-label" id="food-tank-label">{{ $fl->level }}%</span>
                </div>
            </div>
            <div class="tank-status-text" id="food-tank-status">
                {{ $fl->getStatusMessage() }}
            </div>
        </div>

        <!-- Right: Details & Legend -->
        <div class="detail-card">
            <div class="detail-section">
                <h4 style="color: #047857; margin: 0 0 1rem; font-size: 1.05rem;">📋 Details</h4>
                <div class="detail-row">
                    <span class="detail-label">Current Level</span>
                    <span class="detail-value" id="fl-detail-level">{{ $fl->level }}%</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" id="fl-detail-status">
                        @if($fl->status === 'normal')
                            <span style="color: #10b981;">✅ Normal</span>
                        @elseif($fl->status === 'low')
                            <span style="color: #f59e0b;">⚠️ Low</span>
                        @else
                            <span style="color: #ef4444;"></span>
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Can Feed</span>
                    <span class="detail-value" id="fl-detail-canfeed">
                        @if(\App\Models\FoodLevel::canFeed())
                            <span style="color: #10b981;">Yes</span>
                        @else
                            <span style="color: #ef4444;">No</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Last Updated</span>
                    <span class="detail-value" id="fl-detail-updated" style="color: #1f2937; font-weight: 600;">--</span>
                </div>
            </div>

            <div class="legend-card">
                <h5 style="color: #374151; margin: 0 0 0.75rem; font-size: 0.95rem;">🎯 Level Scale</h5>
                <div class="legend-item">
                    <span class="legend-dot" style="background: #10b981;"></span>
                    <span><strong>Normal</strong> — Above 10%</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background: #f59e0b;"></span>
                    <span><strong>Low</strong> — 1%–10%</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background: #ef4444;"></span>
                    <span></span>
                </div>
            </div>

            <!-- Alert Banner -->
            <div class="alert-banner" id="alert-banner"></div>
        </div>
    </div>
</div>

<script>
function createRipple(event) {
    const button = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    button.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
}

let buzzerInterval = null;

function startBuzzer() {
    // browser buzzer removed; empty status is still reflected in UI only
}

function stopBuzzer() {
    // browser buzzer removed
}

let lastFoodStatus = null;

function refreshFoodLevelWithBuzzer() {
    fetch('/api/food-level/current', { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            // Header bar
            const barFill = document.getElementById('header-bar-fill');
            const barText = document.getElementById('header-bar-text');
            const level = data.level ?? 0;
            const detected = data.status !== 'empty';
            if (barFill) {
                barFill.style.width = level + '%';
                barFill.className = 'level-bar-fill ' + data.status;
            }
            if (barText) barText.textContent = detected ? level + '%' : '';

            // Tank
            const tankFill = document.getElementById('food-tank-fill');
            const tankLabel = document.getElementById('food-tank-label');
            const tankStatus = document.getElementById('food-tank-status');
            if (tankFill) {
                tankFill.style.height = level + '%';
                tankFill.className = 'food-tank-fill';
                if (data.status === 'empty') tankFill.classList.add('level-empty');
                else if (data.status === 'low') tankFill.classList.add('level-low');
                else tankFill.classList.add('level-normal');
            }
            if (tankLabel) tankLabel.textContent = data.level + '%';
            if (tankStatus) tankStatus.textContent = data.message;

            // Detail panel
            const dl = document.getElementById('fl-detail-level');
            const ds = document.getElementById('fl-detail-status');
            const dc = document.getElementById('fl-detail-canfeed');
            const du = document.getElementById('fl-detail-updated');

            if (dl) dl.textContent = detected ? 'Detected' : '';
            if (ds) {
                if (data.status === 'normal') ds.innerHTML = '<span style="color:#10b981;">✅ Normal</span>';
                else if (data.status === 'low') ds.innerHTML = '<span style="color:#f59e0b;">⚠️ Low</span>';
                else ds.innerHTML = '';
            }
            if (dc) {
                dc.innerHTML = data.can_feed
                    ? '<span style="color:#10b981;">Yes</span>'
                    : '<span style="color:#ef4444;">No</span>';
            }
            if (du && data.last_updated) {
                du.textContent = new Date(data.last_updated).toLocaleString();
            }

            // Alert banner
            const alert = document.getElementById('alert-banner');
            if (alert) {
                if (data.status === 'empty') {
                    alert.className = 'alert-banner danger';
                    alert.innerHTML = '🚨 <strong>Critical:</strong> Food container is empty! Please refill immediately.';
                } else if (data.status === 'low') {
                    alert.className = 'alert-banner warning';
                    alert.innerHTML = '⚠️ <strong>Warning:</strong> Food level is low. Please refill soon.';
                } else {
                    alert.className = 'alert-banner ok';
                    alert.innerHTML = '✅ Food level is normal. No action needed.';
                }
            }

            // Empty status is shown in the UI; no browser buzzer sound is played.
            lastFoodStatus = data.status;
        })
        .catch(err => console.error('Food level fetch error:', err));
}

document.addEventListener('DOMContentLoaded', function() {
    refreshFoodLevelWithBuzzer();
    setInterval(refreshFoodLevelWithBuzzer, 2000);
});
</script>

<script>
// ─── 🔔 NOTIFICATION BELL SYSTEM (shared via localStorage) ───
const NOTIFICATION_STORAGE_KEY = 'squifm_notifications';

function getNotifications() {
    try { return JSON.parse(localStorage.getItem(NOTIFICATION_STORAGE_KEY) || '[]'); }
    catch { return []; }
}

function saveNotifications(notifications) {
    localStorage.setItem(NOTIFICATION_STORAGE_KEY, JSON.stringify(notifications));
}

function getSeenCount() {
    return parseInt(localStorage.getItem('squifm_notifications_seen') || '0', 10);
}

function setSeenCount(count) {
    localStorage.setItem('squifm_notifications_seen', count.toString());
}

function renderNotifications() {
    if (window.__squifmBellFeedActive) return; // unified sidebar bell owns the notification list
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const notifications = getNotifications();
    if (!list || !badge) return;

    if (notifications.length === 0) {
        list.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
        return;
    }

    const seenCount = getSeenCount();
    const unseenCount = Math.max(0, notifications.length - seenCount);
    
    if (unseenCount > 0) {
        badge.style.display = 'flex';
        badge.textContent = unseenCount;
        badge.classList.add('has-notifications');
    } else {
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
    }

    list.innerHTML = '';
    notifications.forEach(n => {
        const item = document.createElement('div');
        item.className = 'notification-item ' + (n.type || 'success');
        const isOrderNotif = n.message && n.message.includes('Order');
        item.style.cssText = 'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; cursor: ' + (isOrderNotif ? 'pointer' : 'default') + ';';
        item.innerHTML = `
            <span class="notification-icon">${n.icon || (n.type === 'success' ? '✅' : '📅')}</span>
            <div class="notification-content">
                <div class="notification-text">${n.message}</div>
                <div class="notification-time">${n.time}</div>
            </div>
        `;
        if (isOrderNotif) {
            item.onclick = function() {
                goToOrdersTab();
            };
        }
        list.appendChild(item);
    });
}

// Go to Order tab (redirects to inventory page)
function goToOrdersTab() {
    window.location.href = '/inventory?tab=orders';
}

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    if (dropdown) {
        dropdown.classList.toggle('show');
        // Mark all notifications as seen when dropdown is opened
        if (dropdown.classList.contains('show')) {
            const notifications = getNotifications();
            setSeenCount(notifications.length);
            if (badge) {
                badge.textContent = '0';
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    }
}

function clearNotifications(event) {
    event.stopPropagation();
    saveNotifications([]);
    renderNotifications();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    if (bell && dropdown && !bell.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// Render on page load
document.addEventListener('DOMContentLoaded', renderNotifications);

function updateMessagesBadge() {
    try {
        const n    = JSON.parse(localStorage.getItem(NOTIFICATION_STORAGE_KEY) || '[]');
        const seen = parseInt(localStorage.getItem('squifm_messages_seen') || '0', 10);
        const unread = Math.max(0, n.length - seen);
        const b = document.getElementById('messages-nav-badge');
        if (!b) return;
        if (unread > 0) { b.textContent = unread; b.style.display = 'inline-flex'; }
        else { b.style.display = 'none'; }
    } catch(e) {}
}
document.addEventListener('DOMContentLoaded', updateMessagesBadge);
setInterval(updateMessagesBadge, 4000);
</script>

<!-- Logout Modal -->
<style>
</style>

<div class="confirm-modal-overlay" id="logoutModal">
    <div class="confirm-modal">
        <div class="confirm-modal-icon">🚪</div>
        <div class="confirm-modal-title">Confirm Logout</div>
        <div class="confirm-modal-message">Are you sure you want to logout from SQUIFM? Any unsaved data will be lost.</div>
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn cancel" onclick="closeLogoutModal()">Cancel</button>
            <button class="confirm-modal-btn confirm" onclick="confirmLogout()">Yes, Logout</button>
        </div>
    </div>
</div>
</div>

<script>
function showLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.classList.add('show');
    }
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function confirmLogout() {
    const form = document.getElementById('logoutForm');
    if (form) {
        form.submit();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('logoutModal');
    if (modal && e.target === modal) {
        closeLogoutModal();
    }
});
</script>
@endsection