// Notification System Fix
// This file ensures renderNotifications is always available

// Make sure these functions are in global scope
window.renderNotifications = function() {
    // If the sidebar SquifmBell owns the list, defer to it instead of
    // overwriting #notification-list (prevents the two systems from fighting).
    if (window.__squifmBellFeedActive) {
        if (typeof SquifmBell !== 'undefined' && typeof SquifmBell.loadFeed === 'function') {
            SquifmBell.loadFeed();
        }
        return;
    }

    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    
    if (!list || !badge) {
        console.log('Notification elements not found');
        return;
    }
    
    const notifications = getNotifications();
    
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
            <span class="notification-icon">${n.icon || (n.type === 'success'
                ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>'
                : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>')}</span>
            <div class="notification-content">
                <div class="notification-text">${n.message}</div>
                <div class="notification-time">${n.time}</div>
            </div>
        `;
        if (isOrderNotif) {
            item.onclick = function() {
                if (typeof goToOrdersTab === 'function') {
                    goToOrdersTab();
                }
                const dropdown = document.getElementById('notification-dropdown');
                if (dropdown) dropdown.classList.remove('show');
            };
        }
        list.appendChild(item);
    });
};

// Helper functions
window.getNotifications = function() {
    try {
        return JSON.parse(localStorage.getItem('squifm_notifications') || '[]');
    } catch {
        return [];
    }
};

window.saveNotifications = function(notifications) {
    localStorage.setItem('squifm_notifications', JSON.stringify(notifications));
};

window.getSeenCount = function() {
    return parseInt(localStorage.getItem('squifm_notifications_seen') || '0', 10);
};

window.setSeenCount = function(count) {
    localStorage.setItem('squifm_notifications_seen', count.toString());
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof renderNotifications === 'function') {
        renderNotifications();
    }
});

console.log('Notification system fix loaded');
