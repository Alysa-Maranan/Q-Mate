// Customer notification system
let notificationCheckInterval;
let lastNotificationCheck = Date.now();

// Initialize notification system
function initCustomerNotifications() {
    checkForNotifications();
    notificationCheckInterval = setInterval(checkForNotifications, 30000); // Check every 30 seconds
}

// Check for new notifications
async function checkForNotifications() {
    try {
        const response = await fetch('/api/customer/notifications');
        if (!response.ok) return;
        
        const data = await response.json();
        
        if (data.notifications && data.notifications.length > 0) {
            // Show notification badge
            updateNotificationBadge(data.unread_count);
            
            // Check for new notifications since last check
            const newNotifications = data.notifications.filter(n => n.timestamp * 1000 > lastNotificationCheck);
            
            if (newNotifications.length > 0) {
                // Show browser notification for newest one
                showBrowserNotification(newNotifications[0]);
                lastNotificationCheck = Date.now();
            }
        } else {
            updateNotificationBadge(0);
        }
    } catch (error) {
        console.error('Failed to check notifications:', error);
    }
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Show browser notification
function showBrowserNotification(notification) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Escalona\'s Farm', {
            body: notification.message,
            icon: '/favicon.ico',
            badge: '/favicon.ico'
        });
    } else if ('Notification' in window && Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                showBrowserNotification(notification);
            }
        });
    }
}

// Request notification permission on page load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Auto-start on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCustomerNotifications);
} else {
    initCustomerNotifications();
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (notificationCheckInterval) {
        clearInterval(notificationCheckInterval);
    }
});
