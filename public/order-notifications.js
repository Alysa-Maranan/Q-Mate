// ORDER NOTIFICATION SYSTEM - Orders + Cancellations
// This system shows notifications for new customer orders AND cancellations

(function() {
    'use strict';

    // Check for order updates every 10 seconds
    let lastCheckTime = localStorage.getItem('last_order_check') || new Date().toISOString();
    let lastOrderId = parseInt(localStorage.getItem('last_order_id') || '0');
    let lastCancelledId = parseInt(localStorage.getItem('last_cancelled_id') || '0');

    // ── Reliable Audio: single shared AudioContext, unlocked on the first
    // user gesture (browser autoplay policy blocks sound until then) ──
    let sharedAudioCtx = null;
    function ensureAudio() {
        try {
            if (!sharedAudioCtx) {
                const AC = window.AudioContext || window.webkitAudioContext;
                if (!AC) return null;
                sharedAudioCtx = new AC();
            }
            if (sharedAudioCtx.state === 'suspended') sharedAudioCtx.resume();
            return sharedAudioCtx;
        } catch (e) {
            return null;
        }
    }
    ['click', 'keydown', 'touchstart'].forEach(function (evt) {
        document.addEventListener(evt, function audioUnlock() {
            ensureAudio();
            document.removeEventListener(evt, audioUnlock);
        }, { once: true });
    });

    function tone(freqStart, freqEnd, duration, delay = 0) {
        const audioContext = ensureAudio();
        if (!audioContext) return;
        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            const t0 = audioContext.currentTime + delay;
            if (freqEnd && freqEnd !== freqStart) {
                oscillator.frequency.setValueAtTime(freqStart, t0);
                oscillator.frequency.setValueAtTime(freqEnd, t0 + duration * 0.5);
            } else {
                oscillator.frequency.setValueAtTime(freqStart, t0);
            }
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.0001, t0);
            gainNode.gain.exponentialRampToValueAtTime(0.3, t0 + 0.02);
            gainNode.gain.exponentialRampToValueAtTime(0.0001, t0 + duration);

            oscillator.start(t0);
            oscillator.stop(t0 + duration + 0.05);
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    function checkForOrderUpdates() {
        fetch('/api/orders/recent?since=' + encodeURIComponent(lastCheckTime))
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.updates && data.updates.length > 0) {
                    data.updates.forEach(update => {
                        if (update.type === 'new_order' && update.id > lastOrderId) {
                            showNewOrderNotification(update);
                            lastOrderId = update.id;
                            localStorage.setItem('last_order_id', lastOrderId.toString());
                        } else if (update.type === 'cancelled' && update.id > lastCancelledId) {
                            showCancellationNotification(update);
                            lastCancelledId = update.id;
                            localStorage.setItem('last_cancelled_id', lastCancelledId.toString());
                        }
                    });
                }

                // Update check time
                lastCheckTime = new Date().toISOString();
                localStorage.setItem('last_order_check', lastCheckTime);

                // Update badge with total count
                updateNotificationBadge(data.newCount || 0, data.cancelledCount || 0);
                
                // Trigger SquifmBell to refresh
                if (typeof SquifmBell !== 'undefined' && SquifmBell !== null) {
                    SquifmBell.loadFeed();
                }
            })
            .catch(error => {
                console.log('Error checking orders:', error);
            });
    }
    
    function showNewOrderNotification(order) {
        const message = 'New Order: ' + order.details;

        // Add to notification list
        addOrderToNotificationList(message, 'new_order');

        // Play sound
        playNotificationSound();

        // Show browser notification if permitted
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('SQUIFM - New Order', {
                body: message,
                icon: '/favicon.ico'
            });
        }
    }

    function showCancellationNotification(order) {
        const message = 'Order Cancelled: ' + order.details;

        // Add to notification list with warning style
        addOrderToNotificationList(message, 'cancelled');

        // Play distinct sound for cancellation
        playCancellationSound();

        // Show browser notification if permitted
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('SQUIFM - Order Cancelled', {
                body: message,
                icon: '/favicon.ico'
            });
        }
    }
    
    function getNotificationListElement() {
        // The Dashboard's visible Notification section comes first. The bell
        // dropdown (#notification-list) is ALWAYS present in the DOM (even on
        // pages without the bell), so it must never steal the render target.
        return (
            document.getElementById('orderNotificationsContainer') ||
            document.getElementById('notification-list')
        );
    }

    // When the sidebar SquifmBell owns #notification-list (e.g. on the
    // Inventory page), order notifications must go THROUGH the bell instead
    // of overwriting its list, otherwise the two systems fight each other.
    // NOTE: only when the bell is actually rendered on the page — on the
    // Dashboard the bell is hidden, so legacy behavior is preserved there.
    function bellOwnsList() {
        return window.__squifmBellFeedActive &&
            !!document.getElementById('floating-notification-bell') &&
            !!document.getElementById('notification-list') &&
            typeof SquifmBell !== 'undefined';
    }

    function refreshBell() {
        if (typeof SquifmBell !== 'undefined' && typeof SquifmBell.loadFeed === 'function') {
            SquifmBell.loadFeed();
        }
        if (typeof notificationSystem !== 'undefined') {
            notificationSystem.updateNotificationUI();
        }
    }

    function renderOrderItem(list, message, type) {
        // Remove "no notifications" message if exists
        const emptyMsg = list.querySelector('.notification-empty');
        if (emptyMsg) emptyMsg.remove();

        const now = new Date();
        const time = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        const isCancelled = type === 'cancelled';
        const itemClass = isCancelled ? 'notification-item warning' : 'notification-item success';
        const iconSvg = isCancelled
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>';

        const item = document.createElement('div');
        item.className = itemClass;
        item.style.cssText = 'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; cursor: pointer; border-bottom: 1px solid #efebe9;';
        item.innerHTML = `
            <span class="notification-icon" style="font-size: 1.5rem;">
                ${iconSvg}
            </span>
            <div class="notification-content" style="flex: 1;">
                <div class="notification-text" style="font-weight: 600; color: #4e342e; margin-bottom: 0.25rem;">${message}</div>
                <div class="notification-time" style="font-size: 0.75rem; color: #8d6e63;">${time}</div>
            </div>
        `;

        // Click to go to orders tab
        item.onclick = function() {
            goToOrdersTab();
            closeNotificationDropdown();
        };

        // Add to top of list
        list.insertBefore(item, list.firstChild);

        // Keep only last 20 notifications
        while (list.children.length > 20) {
            list.removeChild(list.lastChild);
        }

        // Save to localStorage
        saveNotificationToStorage(message, time, type);
    }

    function addOrderToNotificationList(message, type = 'new_order') {
        // 1) Dashboard's visible Notification section gets it first
        const dashContainer = document.getElementById('orderNotificationsContainer');
        if (dashContainer) {
            renderOrderItem(dashContainer, message, type);
            // Keep the sidebar bell feed in sync as well
            refreshBell();
            return;
        }

        // 2) Pages with the sidebar bell: route through the bell
        if (bellOwnsList()) {
            refreshBell();
            return;
        }

        // 3) Fallback: legacy list
        const list = getNotificationListElement();
        if (!list) return;
        renderOrderItem(list, message, type);
    }
    
    function updateNotificationBadge(newCount, cancelledCount = 0) {
        const badge = document.getElementById('notification-badge');
        if (!badge) return;

        const total = newCount + cancelledCount;
        if (total > 0) {
            badge.textContent = total;
            badge.style.display = 'flex';
            badge.classList.add('has-notifications');

            // Animate bell
            const bell = document.getElementById('notification-bell');
            if (bell) {
                bell.style.animation = 'bellShake 0.5s ease';
                setTimeout(() => bell.style.animation = '', 500);
            }
        } else {
            badge.style.display = 'none';
            badge.classList.remove('has-notifications');
        }
    }

    function playCancellationSound() {
        // Lower two-tone alert for cancellations
        tone(440, 330, 0.3);
    }

    function playNotificationSound() {
        // Pleasant two-tone "ding-dong" (880 Hz → 1175 Hz) for new orders
        tone(880, null, 0.25);
        tone(1175, null, 0.3, 0.22);
    }
    
    function goToOrdersTab() {
        // Check if we're on inventory page
        if (window.location.pathname.includes('inventory')) {
            if (typeof showCategory === 'function') {
                showCategory('orders');
            }
        } else {
            // Redirect to inventory page with orders tab
            window.location.href = '/inventory?tab=orders';
        }
    }
    
    function closeNotificationDropdown() {
        const dropdown = document.getElementById('notification-dropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }
    
    function saveNotificationToStorage(message, time, type = 'new_order') {
        try {
            let notifications = JSON.parse(localStorage.getItem('order_notifications') || '[]');
            notifications.unshift({ message, time, type, timestamp: Date.now() });

            // Keep only last 50
            if (notifications.length > 50) {
                notifications = notifications.slice(0, 50);
            }

            localStorage.setItem('order_notifications', JSON.stringify(notifications));
        } catch (e) {
            console.log('Error saving notification:', e);
        }
    }
    
    function loadNotificationsFromStorage() {
        try {
            const notifications = JSON.parse(localStorage.getItem('order_notifications') || '[]');

            // Dashboard's visible Notification section wins; on bell pages
            // (without a dashboard section) the bell owns the list instead.
            if (!document.getElementById('orderNotificationsContainer') && bellOwnsList()) {
                refreshBell();
                return;
            }
            const list = getNotificationListElement();

            if (!list) return;

            if (notifications.length === 0) {
                list.innerHTML = '<div class="notification-empty" style="padding: 2rem; text-align: center; color: #a1887f;">No order notifications yet.</div>';
                return;
            }

            list.innerHTML = '';
            notifications.forEach(n => {
                const isCancelled = n.type === 'cancelled';
                const itemClass = isCancelled ? 'notification-item warning' : 'notification-item success';
                const iconSvg = isCancelled
                    ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>';

                const item = document.createElement('div');
                item.className = itemClass;
                item.style.cssText = 'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; cursor: pointer; border-bottom: 1px solid #efebe9;';
                item.innerHTML = `
                    <span class="notification-icon" style="font-size: 1.5rem;">
                        ${iconSvg}
                    </span>
                    <div class="notification-content" style="flex: 1;">
                        <div class="notification-text" style="font-weight: 600; color: #4e342e; margin-bottom: 0.25rem;">${n.message}</div>
                        <div class="notification-time" style="font-size: 0.75rem; color: #8d6e63;">${n.time}</div>
                    </div>
                `;
                item.onclick = function() {
                    goToOrdersTab();
                    closeNotificationDropdown();
                };
                list.appendChild(item);
            });

            // If we're rendering into the dashboard container, re-apply the expected container height scroll behavior
            if (list.id === 'orderNotificationsContainer') {
                list.style.overflowY = 'auto';
            }
        } catch (e) {
            console.log('Error loading notifications:', e);
        }
    }
    
    // Toggle notification dropdown
    window.toggleNotificationDropdown = function() {
        const dropdown = document.getElementById('notification-dropdown');
        const badge = document.getElementById('notification-badge');
        
        if (dropdown) {
            dropdown.classList.toggle('show');
            
            // Clear badge when opened
            if (dropdown.classList.contains('show') && badge) {
                badge.textContent = '0';
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    };
    
    // Clear all notifications
    window.clearAllNotifications = function(event) {
        if (event) event.stopPropagation();
        
        if (confirm('Clear all order notifications?')) {
            localStorage.removeItem('order_notifications');
            const list = document.getElementById('notification-list');
            if (list) {
                list.innerHTML = '<div class="notification-empty" style="padding: 2rem; text-align: center; color: #a1887f;">No order notifications yet.</div>';
            }
            
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    };
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const bell = document.getElementById('notification-bell');
        const dropdown = document.getElementById('notification-dropdown');
        
        if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadNotificationsFromStorage();

        // Start checking for order updates (new orders + cancellations)
        checkForOrderUpdates();
        setInterval(checkForOrderUpdates, 10000); // Check every 10 seconds
    });

    console.log('Order Notification System Loaded');
})();
