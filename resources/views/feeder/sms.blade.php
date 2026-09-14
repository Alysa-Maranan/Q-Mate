@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /*  Nav - Brown Theme matching Learn Book */
    .nav-bar {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        border-bottom: 2px solid #d7ccc8;
        box-shadow: 0 4px 20px rgba(121, 85, 72, 0.08);
        padding: 1.25rem 3rem;
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 1000;
    }
    .logo-section { display: flex; align-items: center; gap: 1rem; }
    .logo {
        width: 50px; height: 50px; border-radius: 14px; background: white;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(121, 85, 72, 0.18);
        border: 2px solid #a1887f; overflow: hidden;
    }
    .logo img { width: 100%; height: 100%; object-fit: cover; }
    .logo-text { color: #6d4c41; font-size: 1.5rem; font-weight: 800; letter-spacing: 0.5px; }
    .nav-menu { display: flex; gap: 0.5rem; align-items: center; }
    .nav-item {
        color: #6d4c41; text-decoration: none; font-weight: 700; font-size: 1rem;
        padding: 0.75rem 1.5rem; border-radius: 10px; transition: all 0.3s ease;
        background: #fffdfa; border: 2px solid #a1887f;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.10);
        position: relative; overflow: hidden;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .nav-item:hover { background: #f8ede3; box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18); }
    .nav-item.active { background: #a1887f; color: #fffdfa; box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5); }

    /* 🔔 Notification Bell - Brown Theme */
    .notification-bell {
        position: relative; cursor: pointer; padding: 0.5rem; font-size: 1.5rem;
        background: white; border-radius: 50%; width: 45px; height: 45px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #a1887f; box-shadow: 0 4px 12px rgba(141, 110, 99, 0.2); transition: all 0.3s ease;
    }
    .notification-bell:hover { background: #f8ede3; box-shadow: 0 6px 16px rgba(141, 110, 99, 0.3); transform: scale(1.1); }
    .notification-badge {
        position: absolute; top: -5px; right: -5px;
        background: #ef4444; color: white; border-radius: 50%;
        width: 20px; height: 20px; font-size: 0.75rem; font-weight: bold;
        display: flex; align-items: center; justify-content: center; border: 2px solid white;
    }
    .notification-badge.has-notifications { animation: pulse-badge 2s infinite; }
    @keyframes pulse-badge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
    .notification-dropdown {
        position: absolute; top: 60px; right: 0; width: 350px; max-height: 400px;
        overflow-y: auto; background: white; border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid #d7ccc8;
        z-index: 1001; display: none;
    }
    .notification-dropdown.show { display: block; animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .notification-header { padding: 1rem; border-bottom: 1px solid #d7ccc8; font-weight: 700; color: #6d4c41; display: flex; justify-content: space-between; align-items: center; }
    .notification-item { padding: 0.75rem 1rem; border-bottom: 1px solid #efebe9; display: flex; align-items: flex-start; gap: 0.5rem; transition: background 0.2s ease; }
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

    /*  Page  */
    .page-wrapper { max-width: 900px; margin: 2rem auto; padding: 0 2rem 4rem; }

    /*  Chat Window  */
    .chat-window {
        background: white;
        border-radius: 28px;
        box-shadow: 0 12px 60px rgba(134,239,172,0.25), 0 4px 16px rgba(0,0,0,0.07);
        border: 1.5px solid #d1fae5;
        overflow: hidden; display: flex; flex-direction: column;
        height: 82vh; min-height: 560px;
    }

    /*  Header  */
    .chat-header {
        background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
        padding: 1.35rem 2rem;
        display: flex; align-items: center; gap: 1.25rem; flex-shrink: 0;
    }
    .chat-avatar-wrap { position: relative; }
    .chat-avatar {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; border: 2.5px solid rgba(255,255,255,0.6);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .online-dot {
        position: absolute; bottom: 2px; right: 2px;
        width: 14px; height: 14px; border-radius: 50%;
        background: #86efac; border: 2.5px solid #15803d;
    }
    .chat-header-info { flex: 1; }
    .chat-contact-name { color: white; font-weight: 800; font-size: 1.2rem; margin: 0 0 0.2rem; }
    .chat-contact-sub  { color: rgba(255,255,255,0.82); font-size: 0.85rem; margin: 0; }
    .clear-btn {
        background: rgba(255,255,255,0.18); color: white;
        border: 1.5px solid rgba(255,255,255,0.35); border-radius: 999px;
        padding: 0.5rem 1.2rem; font-size: 0.88rem; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
    }
    .clear-btn:hover { background: rgba(255,255,255,0.3); }

    /*  Messages  */
    .messages-area {
        flex: 1; overflow-y: auto;
        padding: 2rem 2.5rem;
        display: flex; flex-direction: column; gap: 1.2rem;
        background: #f0fdf4;
    }
    .messages-area::-webkit-scrollbar { width: 6px; }
    .messages-area::-webkit-scrollbar-track { background: transparent; }
    .messages-area::-webkit-scrollbar-thumb { background: #bbf7d0; border-radius: 999px; }

    /* Date separator */
    .date-sep { text-align: center; font-size: 0.78rem; color: #6b7280; font-weight: 600; margin: 0.5rem 0; }
    .date-sep span { background: #dcfce7; padding: 0.2rem 1rem; border-radius: 999px; border: 1px solid #bbf7d0; }

    /* Bubble row  LEFT aligned (received) */
    .bubble-row { display: flex; justify-content: flex-start; align-items: flex-end; gap: 0.85rem; }
    .sender-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: linear-gradient(135deg, #86efac, #22c55e);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0; margin-bottom: 2px;
        box-shadow: 0 2px 10px rgba(74,222,128,0.35); border: 2px solid white;
    }
    .bubble-wrap { display: flex; flex-direction: column; align-items: flex-start; max-width: 70%; }
    .sender-label { font-size: 0.75rem; color: #6b7280; font-weight: 700; margin-bottom: 0.35rem; padding-left: 0.2rem; }

    .bubble {
        background: white; color: #1f2937;
        border-radius: 4px 20px 20px 20px;
        padding: 1rem 1.35rem;
        font-size: 1rem; line-height: 1.65;
        box-shadow: 0 2px 12px rgba(0,0,0,0.09);
        border: 1.5px solid #e5e7eb;
    }
    .bubble.success { border-left: 4px solid #22c55e; }
    .bubble.alert   { border-left: 4px solid #ef4444; background: #fff5f5; }
    .bubble.info    { border-left: 4px solid #3b82f6; }

    .bubble-msg { display: flex; align-items: flex-start; gap: 0.55rem; font-size: 1rem; font-weight: 500; }
    .bubble-meta { display: flex; align-items: center; gap: 0.55rem; margin-top: 0.5rem; }
    .bubble-time { font-size: 0.73rem; color: #9ca3af; }
    .bubble-badge { font-size: 0.7rem; border-radius: 999px; padding: 0.15rem 0.65rem; font-weight: 700; }
    .bubble-badge.success { background: #dcfce7; color: #16a34a; }
    .bubble-badge.alert   { background: #fee2e2; color: #dc2626; }
    .bubble-badge.info    { background: #dbeafe; color: #1d4ed8; }

    /* Empty state */
    .empty-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 3rem; }
    .empty-icon  { font-size: 5.5rem; opacity: 0.45; margin-bottom: 1.2rem; }
    .empty-title { font-size: 1.35rem; font-weight: 800; color: #374151; margin-bottom: 0.5rem; }
    .empty-sub   { font-size: 0.95rem; color: #9ca3af; max-width: 380px; }

    /* Footer */
    .chat-footer {
        padding: 1.1rem 2rem; background: white; border-top: 1.5px solid #d1fae5;
        display: flex; align-items: center; gap: 1rem; flex-shrink: 0;
    }
    .footer-note {
        flex: 1; background: #f9fafb; border-radius: 999px;
        padding: 0.85rem 1.5rem; font-size: 0.9rem; color: #9ca3af;
        border: 1.5px solid #e5e7eb;
    }

    /* Animations */
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-14px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .bubble-row { animation: fadeInLeft 0.25s ease both; }

    .ripple { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.4); transform: scale(0); animation: rippleAnim 0.5s linear; pointer-events: none; }
    @keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }
</style>

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="page-wrapper">
    <div class="chat-window">

        <!-- Header -->
        <div class="chat-header">
            <div class="chat-avatar-wrap">
                <div class="chat-avatar">🐦</div>
                <div class="online-dot"></div>
            </div>
            <div class="chat-header-info">
                <p class="chat-contact-name">SQUIFM Feeder</p>
                <p class="chat-contact-sub">09916624892 · Feeding notifications</p>
            </div>
            <button class="clear-btn" onclick="clearMessages()">🗑 Clear All</button>
        </div>

        <!-- Messages -->
        <div class="messages-area" id="messagesArea"></div>

        <!-- Footer -->
        <div class="chat-footer">
            <div class="footer-note">Notifications appear here automatically when feeding is triggered.</div>
        </div>

    </div>
</div>

<script>
const STORAGE_KEY = 'squifm_notifications';

function getNotifications() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch { return []; }
}

function iconFor(type) {
    if (type === 'success') return '✅';
    if (type === 'alert')   return '🚨';
    return '📅';
}

function badgeFor(type) {
    if (type === 'success') return 'Feeding Done';
    if (type === 'alert')   return 'Alert';
    return 'Scheduled';
}

function fmtTime(t) {
    try {
        const d = new Date(t);
        return isNaN(d) ? t : d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    } catch { return t; }
}

function fmtDate(t) {
    try {
        const d   = new Date(t);
        const now = new Date();
        const tod = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const yes = new Date(tod); yes.setDate(yes.getDate() - 1);
        const day = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        if (day.getTime() === tod.getTime()) return 'Today';
        if (day.getTime() === yes.getTime()) return 'Yesterday';
        return d.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
    } catch { return t; }
}

// On Messages page: badge is always hidden (we are reading right now)
function updateMessagesBadge() {
    const b = document.getElementById('messages-nav-badge');
    if (b) b.style.display = 'none';
}

function clearMessages() {
    if (!confirm('Clear all notifications?')) return;
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(SEEN_KEY);
    render();
}

function createRipple(e) {
    const btn = e.currentTarget;
    const r   = document.createElement('span');
    r.classList.add('ripple');
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    r.style.width  = r.style.height = size + 'px';
    r.style.left   = (e.clientX - rect.left - size / 2) + 'px';
    r.style.top    = (e.clientY - rect.top  - size / 2) + 'px';
    btn.appendChild(r);
    setTimeout(() => r.remove(), 600);
}

// ── Mark as read: clear badge when this page is open ──
const SEEN_KEY = 'squifm_messages_seen';
function markRead() {
    const n = getNotifications();
    localStorage.setItem(SEEN_KEY, n.length);
    const b = document.getElementById('messages-nav-badge');
    if (b) b.style.display = 'none';
}

// ── Render feeder notifications only ──
function render() {
    const area   = document.getElementById('messagesArea');
    const notifs = getNotifications();

    if (!notifs.length) {
        area.innerHTML = `
          <div class="empty-state">
            <div class="empty-icon">💬</div>
            <p class="empty-title">No messages yet</p>
            <p class="empty-sub">Every notification from the feeder — manual feeds, scheduled feeds, and alerts — will appear here.</p>
          </div>`;
        markRead();
        return;
    }

    const list = [...notifs].sort((a, b) => new Date(a.time) - new Date(b.time));
    let html = '';
    let lastDate = null;

    list.forEach(n => {
        const dl = fmtDate(n.time);
        if (dl !== lastDate) {
            html += `<div class="date-sep"><span>${dl}</span></div>`;
            lastDate = dl;
        }
        const type  = n.type || 'info';
        const icon  = iconFor(type);
        const badge = badgeFor(type);
        const time  = fmtTime(n.time);

        html += `
          <div class="bubble-row">
            <div class="sender-avatar">${icon}</div>
            <div class="bubble-wrap">
              <div class="sender-label">09916624892</div>
              <div class="bubble ${type}">
                <div class="bubble-msg"><span>${icon}</span><span>${n.message}</span></div>
                <div class="bubble-meta">
                  <span class="bubble-badge ${type}">${badge}</span>
                  <span class="bubble-time">${time}</span>
                </div>
              </div>
            </div>
          </div>`;
    });

    area.innerHTML = html;
    area.scrollTop = area.scrollHeight;
    markRead();
    updateMessagesBadge();
}

render();
setInterval(render, 4000);
</script>

<script>
// 🔔 Notification Bell (shared localStorage)
const NOTIFICATION_STORAGE_KEY = 'squifm_notifications';

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
    const list  = document.getElementById('notification-list');
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
    render(); // also refresh chat bubbles
}

document.addEventListener('click', function(e) {
    const bell     = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    if (bell && dropdown && !bell.contains(e.target)) dropdown.classList.remove('show');
});

document.addEventListener('DOMContentLoaded', renderNotifications);
setInterval(renderNotifications, 4000);

// Logout Modal Functions
function showLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.classList.add('show');
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) modal.classList.remove('show');
}

function confirmLogout() {
    const form = document.getElementById('logoutForm');
    if (form) form.submit();
}

// Close modal when clicking outside of it
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeLogoutModal();
        });
    }
});
</script>

<!-- Logout Modal CSS -->
<style>
</style>

<!-- Logout Modal HTML -->
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

@endsection
