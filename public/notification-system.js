// Notification System with Sound and Flash Effects
class NotificationSystem {
    constructor() {
        this.notifications = [];
        this.notificationBell = document.getElementById('notification-bell');
        this.notificationBadge = document.getElementById('notification-badge');
        this.notificationList = document.getElementById('notification-list');
        this.maxNotifications = 10;
        this.audioContext = null;
        this.clearedNotifications = new Set(); // Track cleared notification messages
        this.loadClearedNotifications();
        this.loadNotifications(); // Load saved notifications
        this.initAudio();
    }

    loadNotifications() {
        try {
            const saved = localStorage.getItem('squifm_notifications');
            if (saved) {
                const notificationsArray = JSON.parse(saved);
                this.notifications = notificationsArray.filter(n => n && n.message); // Validate
                // Update UI with loaded notifications
                this.updateNotificationUI();
            }
        } catch (e) {
            console.log('Could not load notifications from storage');
            this.notifications = [];
        }
    }

    saveNotifications() {
        try {
            localStorage.setItem('squifm_notifications', JSON.stringify(this.notifications));
        } catch (e) {
            console.log('Could not save notifications to storage');
        }
    }

    loadClearedNotifications() {
        const cleared = localStorage.getItem('sqifm_cleared_notifications');
        const clearedTime = localStorage.getItem('sqifm_cleared_time');
        
        // Clear the cleared list after 1 hour
        if (clearedTime) {
            const hourAgo = Date.now() - (60 * 60 * 1000);
            if (parseInt(clearedTime) < hourAgo) {
                localStorage.removeItem('sqifm_cleared_notifications');
                localStorage.removeItem('sqifm_cleared_time');
                this.clearedNotifications = new Set();
                return;
            }
        }
        
        if (cleared) {
            try {
                const clearedArray = JSON.parse(cleared);
                this.clearedNotifications = new Set(clearedArray);
            } catch (e) {
                this.clearedNotifications = new Set();
            }
        }
    }

    saveClearedNotifications() {
        localStorage.setItem('sqifm_cleared_notifications', JSON.stringify(Array.from(this.clearedNotifications)));
        localStorage.setItem('sqifm_cleared_time', Date.now().toString());
    }

    initAudio() {
        // Lazy init - create AudioContext only on first user gesture
        document.addEventListener('click', () => {
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            } else if (this.audioContext.state === 'suspended') {
                this.audioContext.resume();
            }
        }, { once: false });
    }

    playNotificationSound() {
        try {
            const audioContext = this.audioContext;
            if (!audioContext || audioContext.state === 'suspended') return;

            // Create a pleasant notification sound using oscillators
            const now = audioContext.currentTime;
            
            // First beep (higher pitch)
            const osc1 = audioContext.createOscillator();
            const gain1 = audioContext.createGain();
            osc1.connect(gain1);
            gain1.connect(audioContext.destination);
            
            osc1.frequency.value = 800;
            gain1.gain.setValueAtTime(0.3, now);
            gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
            
            osc1.start(now);
            osc1.stop(now + 0.1);

            // Second beep (higher pitch)
            const osc2 = audioContext.createOscillator();
            const gain2 = audioContext.createGain();
            osc2.connect(gain2);
            gain2.connect(audioContext.destination);
            
            osc2.frequency.value = 1000;
            gain2.gain.setValueAtTime(0.3, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.22);
            
            osc2.start(now + 0.12);
            osc2.stop(now + 0.22);
        } catch (error) {
            console.log('Audio notification unavailable');
        }
    }

    flashBell() {
        if (!this.notificationBell) return;

        // Add flash animation
        this.notificationBell.style.animation = 'none';
        setTimeout(() => {
            this.notificationBell.style.animation = 'bellFlash 0.6s ease-in-out';
        }, 10);
    }

addNotification(message, type = 'info', icon = null, source = null) {
    // Default icon based on type
    if (!icon) {
        if (type === 'success') {
            icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        } else if (type === 'error') {
            icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        } else if (type === 'warning') {
            icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        } else {
            icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        }
    }

    // Create notification object
    const notification = {
        id: Date.now(),
        message: message,
        type: type, // 'success', 'info', 'warning', 'error'
        icon: icon,
        source: source,
        timestamp: new Date().toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    };

    // Add to notifications array
    this.notifications.unshift(notification);

    // Limit notifications to max
    if (this.notifications.length > this.maxNotifications) {
        this.notifications.pop();
    }

        // Save to localStorage
        this.saveNotifications();

        // Play sound
        this.playNotificationSound();

        // Flash bell
        this.flashBell();

        // Update UI
        this.updateNotificationUI();

        // Trigger SquifmBell to refresh (sidebar notification system)
        this.triggerSquifmBellRefresh();

        // Don't auto-dismiss - let user clear manually or stay until they close browser
        // This way notifications stay in the bell dropdown
        return notification.id;
    }

    // Trigger SquifmBell to refresh (sidebar notification system)
    triggerSquifmBellRefresh() {
        // Only relevant on pages where the bell is actually rendered
        if (!document.getElementById('floating-notification-bell')) return;
        // Try to refresh the SquifmBell if it exists on the page
        if (typeof SquifmBell !== 'undefined' && SquifmBell !== null) {
            SquifmBell.loadFeed();
        }
    }

    removeNotification(notificationId) {
        this.notifications = this.notifications.filter(n => n.id !== notificationId);
        this.saveNotifications(); // Save after removing
        this.updateNotificationUI();
    }

    updateNotificationUI() {
        // If the sidebar SquifmBell owns #notification-list on this page,
        // don't render our own list into it — just ask the bell to refresh.
        // Only when the bell is actually rendered (Dashboard hides the bell,
        // so legacy behavior is preserved there).
        if (window.__squifmBellFeedActive && document.getElementById('floating-notification-bell')) {
            if (typeof SquifmBell !== 'undefined' && typeof SquifmBell.loadFeed === 'function') {
                SquifmBell.loadFeed();
            }
            return;
        }

        // Update badge
        const count = this.notifications.length;
        if (count > 0) {
            this.notificationBadge.textContent = count;
            this.notificationBadge.style.display = 'flex';
        } else {
            this.notificationBadge.style.display = 'none';
        }

        // Update notification list
        if (this.notifications.length === 0) {
            this.notificationList.innerHTML = `
                <div class="notification-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" style="margin-bottom: 1rem; opacity: 0.7;">
                        <defs>
                            <radialGradient id="glossy-btn-empty" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" style="stop-color:#FFFFFF;stop-opacity:0.8" />
                                <stop offset="50%" style="stop-color:#FFF9E6;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#E8D4B8;stop-opacity:1" />
                            </radialGradient>
                            <radialGradient id="golden-bell-empty" cx="40%" cy="30%" r="70%">
                                <stop offset="0%" style="stop-color:#FFED4E;stop-opacity:1" />
                                <stop offset="40%" style="stop-color:#FFD700;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#B8860B;stop-opacity:1" />
                            </radialGradient>
                            <filter id="soft-shadow-empty">
                                <feGaussianBlur in="SourceGraphic" stdDeviation="1.5" />
                                <feOffset dx="0" dy="2" result="offsetblur" />
                                <feComponentTransfer>
                                    <feFuncA type="linear" slope="0.15" />
                                </feComponentTransfer>
                                <feMerge>
                                    <feMergeNode />
                                    <feMergeNode in="SourceGraphic" />
                                </feMerge>
                            </filter>
                        </defs>
                        <circle cx="12" cy="14" r="10" fill="#00000015" filter="url(#soft-shadow-empty)" />
                        <circle cx="12" cy="12" r="11" fill="url(#glossy-btn-empty)" stroke="#E8D4B8" stroke-width="0.5" />
                        <circle cx="11" cy="10" r="7" fill="#FFFFFF" opacity="0.3" />
                        <g transform="translate(12, 12) rotate(-8)" transform-origin="0 0">
                            <ellipse cx="0" cy="-2" rx="3" ry="2.5" fill="url(#golden-bell-empty)" stroke="#8B6914" stroke-width="0.6" />
                            <path d="M-3 -1.5 Q-3 2 0 4 Q3 2 3 -1.5" fill="url(#golden-bell-empty)" stroke="#8B6914" stroke-width="0.6" />
                            <ellipse cx="-1" cy="0" rx="1.5" ry="2" fill="#FFFF99" opacity="0.4" />
                            <circle cx="0" cy="3" r="0.7" fill="#8B6914" />
                        </g>
                    </svg>
                    <p>No notifications yet</p>
                    <p style="font-size: 0.75rem; color: #a1887f; margin-top: 0.5rem;">You're all caught up!</p>
                </div>
            `;
        } else {
            this.notificationList.innerHTML = this.notifications.map(notif => `
                <div class="notification-item ${notif.type}" style="animation: slideDown 0.3s ease;">
                    <div class="notification-icon">${notif.icon}</div>
                    <div class="notification-content">
                        <div class="notification-text">${notif.message}</div>
                        <div class="notification-time">
                            ${notif.source ? `<span style="color:#a1887f;font-weight:600;">${notif.source}</span>` : ''}
                            <span>${notif.timestamp}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }

    // Notification helpers with SVG icons
    success(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        return this.addNotification(message, 'success', icon);
    }

    info(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        return this.addNotification(message, 'info', icon);
    }

    warning(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        return this.addNotification(message, 'warning', icon);
    }

    error(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        return this.addNotification(message, 'error', icon);
    }

    order(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
        return this.addNotification(message, 'info', icon, 'Orders & Products');
    }

    feeder(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path><path d="M12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"></path></svg>';
        return this.addNotification(message, 'success', icon, 'Feeder');
    }

    temperature(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path></svg>';
        return this.addNotification(message, 'info', icon, 'Temperature & Humidity');
    }

    inventory(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
        return this.addNotification(message, 'info', icon, 'Inventory & Sales');
    }

    chat(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
        return this.addNotification(message, 'info', icon, 'Customer Chat');
    }

    detection(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
        return this.addNotification(message, 'info', icon, 'Quail Detection');
    }

    learnbook(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>';
        return this.addNotification(message, 'info', icon, 'Learn Book');
    }

    settings(message, icon = null) {
        if (!icon) icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>';
        return this.addNotification(message, 'info', icon, 'Settings');
    }
}

// Initialize notification system
var notificationSystem;

document.addEventListener('DOMContentLoaded', function() {
    notificationSystem = new NotificationSystem();

    // Add bell flash animation to CSS
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes bellFlash {
                0% { transform: scale(1); }
                25% { transform: scale(1.15); opacity: 1; }
                50% { transform: scale(1); opacity: 0.7; }
                75% { transform: scale(1.15); opacity: 1; }
                100% { transform: scale(1); }
            }

            @keyframes slideDown {
                from { 
                    opacity: 0; 
                    transform: translateY(-10px);
                }
                to { 
                    opacity: 1; 
                    transform: translateY(0);
                }
            }

            .notification-item {
                animation: slideDown 0.3s ease !important;
            }
        `;
        document.head.appendChild(style);
    }
});

// Global function to access notification system
window.notify = {
    success: (msg, icon) => notificationSystem?.success(msg, icon),
    info: (msg, icon) => notificationSystem?.info(msg, icon),
    warning: (msg, icon) => notificationSystem?.warning(msg, icon),
    error: (msg, icon) => notificationSystem?.error(msg, icon),
    order: (msg, icon) => notificationSystem?.order(msg, icon),
    feeder: (msg, icon) => notificationSystem?.feeder(msg, icon),
    temperature: (msg, icon) => notificationSystem?.temperature(msg, icon),
    inventory: (msg, icon) => notificationSystem?.inventory(msg, icon),
};
