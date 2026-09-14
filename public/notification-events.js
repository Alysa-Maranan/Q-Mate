/**
 * Global Notification Event System
 * This file handles triggering notifications from different pages and events
 * Works with notification-system.js
 */


document.addEventListener('DOMContentLoaded', function() {
    // Initialize NotificationSystem if it exists (and only once — it may
    // already have been created by notification-system.js).
    if (typeof NotificationSystem !== 'undefined' && (typeof notificationSystem === 'undefined' || !notificationSystem)) {
        notificationSystem = new NotificationSystem();
        
        // Set up global notification functions
        window.triggerNotification = triggerNotification;
        window.notifyNavigation = notifyNavigation;
        window.notifyOrderCreated = notifyOrderCreated;
        window.notifyOrderUpdated = notifyOrderUpdated;
        window.notifyInventoryChange = notifyInventoryChange;
        window.notifyFeederEvent = notifyFeederEvent;
        window.notifySystemAlert = notifySystemAlert;
        window.notifyScheduleEvent = notifyScheduleEvent;
    }
});

/**
 * Generic notification trigger
 * @param {string} message - Notification message
 * @param {string} type - Type: 'success', 'info', 'warning', 'error'
 * @param {string} icon - SVG icon to display (optional)
 */
function triggerNotification(message, type = 'info', icon = null) {
    if (!notificationSystem) return;
    notificationSystem.addNotification(message, type, icon);
}

/**
 * Notify navigation between sidebar items
 * Call this when user navigates to different sections
 * @param {string} pageTitle - Name of the page
 * @param {string} emoji - Page emoji
 */
function notifyNavigation(pageTitle) {
    // Navigation notifications disabled
    // if (notificationSystem) {
    //     const message = `Navigated to ${pageTitle}`;
    //     notificationSystem.addNotification(message, 'info', emoji);
    // }
    // 
    // // Also log to dashboard notifications
    // addDashboardNotification(pageTitle, emoji);
}

/**
 * Add notification to dashboard
 * Logs activities to the dashboard's notification system
 * @param {string} pageTitle - Page/section name
 * @param {string} emoji - Emoji for the section
 */
function addDashboardNotification(pageTitle) {
    // Map pageTitle to category
    const categoryMap = {
        'Feeder System': 'feeder',
        'Temperature & Humidity': 'temperature',
        'Inventory & Sales': 'inventory',
        'Quail Detection': 'detection',
        'Learn Book': 'learn',
        'Settings': 'settings',
        'Dashboard': 'dashboard'
    };
    
    const category = categoryMap[pageTitle] || 'dashboard';
    const timestamp = new Date().toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    
    // Get existing notifications
    let notifications = JSON.parse(localStorage.getItem('dashboardNotifications') || '[]');
    
    // Add new notification
    notifications.unshift({
        id: Date.now(),
        category: category,
        title: `Navigated to ${pageTitle}`,
        message: `You accessed the ${pageTitle} section`,
        timestamp: timestamp
    });
    
    // Keep only last 20 notifications
    if (notifications.length > 20) {
        notifications = notifications.slice(0, 20);
    }
    
    localStorage.setItem('dashboardNotifications', JSON.stringify(notifications));
    
    // Dispatch custom event to update dashboard if it's open in another tab
    window.dispatchEvent(new Event('storage'));
    
    // Update dashboard if on the same page
    if (typeof updateNotificationDisplay === 'function') {
        updateNotificationDisplay();
    }
}

/**
 * Notify order creation
 * Call from order form when new order is created
 * @param {string} orderId - Order ID
 * @param {string} customerName - Customer name
 * @param {number} totalAmount - Order amount
 */
function notifyOrderCreated(orderId, customerName, totalAmount) {
    if (!notificationSystem) return;
    const message = `New Order #${orderId} from ${customerName} - ₱${totalAmount.toLocaleString()}`;
    notificationSystem.addNotification(message, 'success', null, 'Orders & Products');
}

/**
 * Notify order status update
 * @param {string} orderId - Order ID
 * @param {string} status - New status
 */
function notifyOrderUpdated(orderId, status) {
    if (!notificationSystem) return;
    const message = `Order #${orderId} status: ${status.toUpperCase()}`;
    notificationSystem.addNotification(
        message,
        status === 'cancelled' ? 'warning' : 'info',
        null,
        'Orders & Products'
    );
}

/**
 * Notify inventory changes
 * @param {string} action - 'added', 'updated', 'low'
 * @param {string} itemName - Item name
 * @param {number} quantity - Quantity
 */
function notifyInventoryChange(action, itemName, quantity) {
    if (!notificationSystem) return;
    const messages = {
        'added': `Added ${quantity} ${itemName} to inventory`,
        'updated': `Updated ${itemName}: ${quantity} items`,
        'low': `Low inventory alert: ${itemName} (${quantity} left)`
    };
    const types = {
        'added': 'success',
        'updated': 'info',
        'low': 'warning'
    };
    
    notificationSystem.addNotification(
        messages[action] || `Inventory change: ${itemName}`,
        types[action] || 'info',
        null,
        'Inventory & Sales'
    );
    
    // Also log to dashboard
    addDashboardActivityNotification('inventory', 
        `${action.charAt(0).toUpperCase() + action.slice(1)} Inventory`, 
        messages[action] || `Inventory change: ${itemName}`);
}

/**
 * Notify feeder-related events
 * @param {string} action - 'refill', 'maintenance', 'error'
 * @param {string} details - Additional details
 */
function notifyFeederEvent(action, details = '') {
    if (!notificationSystem) return;
    const messages = {
        'refill': `Feeder refilled - ${details || 'Ready for feeding'}`,
        'maintenance': `Feeder maintenance scheduled - ${details}`,
        'error': `Feeder error - ${details || 'Check system'}`
    };
    const types = {
        'refill': 'success',
        'maintenance': 'warning',
        'error': 'error'
    };
    
    notificationSystem.addNotification(
        messages[action] || `Feeder event: ${details}`,
        types[action] || 'info',
        null,
        'Feeder'
    );
    
    // Also log to dashboard
    addDashboardActivityNotification('feeder',
        `${action.charAt(0).toUpperCase() + action.slice(1)} - Feeder`,
        messages[action] || `Feeder event: ${details}`);
}

/**
 * Notify feeder feeding status (success/failed)
 * @param {string} status - 'success' or 'failed'
 * @param {string} duration - Feeding duration in seconds
 * @param {string} breed - Quail breed name
 * @param {string} feedType - 'scheduled' or 'manual'
 */
function notifyFeederFeedingStatus(status, duration, breed = 'Quails', feedType = 'scheduled') {
    if (!notificationSystem) return;
    
    const isSuccess = status === 'success' || status === 'completed';
    const title = isSuccess ? 'Feeding Completed' : 'Feeding Failed';
    const message = isSuccess 
        ? `${breed} feeding completed - ${duration} seconds`
        : `${breed} feeding failed - ${duration} seconds`;
    const type = isSuccess ? 'success' : 'error';
    
    notificationSystem.addNotification(message, type);
    
    // Also log to dashboard
    addDashboardActivityNotification('feeder',
        `${title} - ${feedType.charAt(0).toUpperCase() + feedType.slice(1)}`,
        message);
}

/**
 * Notify system alerts
 * @param {string} alertType - Type of alert
 * @param {string} message - Alert message
 */
function notifySystemAlert(alertType, message) {
    if (!notificationSystem) return;
    const alerts = {
        'temperature': { type: 'warning' },
        'humidity': { type: 'warning' },
        'power': { type: 'error' },
        'maintenance': { type: 'warning' },
        'backup': { type: 'info' }
    };
    
    const alert = alerts[alertType] || { type: 'warning' };
    notificationSystem.addNotification(message, alert.type, null, 'Settings');
    
    // Also log to dashboard
    const categoryMap = {
        'temperature': 'temperature',
        'humidity': 'temperature',
        'power': 'settings',
        'maintenance': 'settings',
        'backup': 'settings'
    };
    addDashboardActivityNotification(categoryMap[alertType] || 'settings',
        `System Alert: ${alertType.charAt(0).toUpperCase() + alertType.slice(1)}`,
        message);
}

/**
 * Notify temperature/humidity monitoring events
 * @param {string} status - 'normal', 'warning', 'critical'
 * @param {number} temperature - Current temperature
 * @param {number} humidity - Current humidity
 * @param {string} message - Custom message
 */
function notifyTemperatureHumidityStatus(status, temperature, humidity, message = '') {
    if (!notificationSystem) return;
    
    const statuses = {
        'normal': { type: 'info', title: 'Temperature & Humidity Normal' },
        'warning': { type: 'warning', title: 'Temperature & Humidity Warning' },
        'critical': { type: 'error', title: 'Temperature & Humidity Critical' }
    };
    
    const statusInfo = statuses[status] || statuses['normal'];
    const displayMsg = message || `Temp: ${temperature.toFixed(1)}°C, Humidity: ${humidity.toFixed(1)}%`;
    
    notificationSystem.addNotification(displayMsg, statusInfo.type, null, 'Temperature & Humidity');
    
    // Also log to dashboard
    addDashboardActivityNotification('temperature',
        statusInfo.title,
        displayMsg);
}

/**
 * Notify schedule events
 * @param {string} eventType - 'scheduled', 'started', 'completed'
 * @param {string} taskName - Name of scheduled task
 */
function notifyScheduleEvent(eventType, taskName) {
    if (!notificationSystem) return;
    const messages = {
        'scheduled': `Task scheduled: ${taskName}`,
        'started': `Task started: ${taskName}`,
        'completed': `Task completed: ${taskName}`
    };
    const types = {
        'scheduled': 'info',
        'started': 'info',
        'completed': 'success'
    };
    
    notificationSystem.addNotification(
        messages[eventType] || `Schedule update: ${taskName}`,
        types[eventType] || 'info',
        null,
        'Settings'
    );
    
    // Also log to dashboard
    addDashboardActivityNotification('settings',
        `Task ${eventType}: ${taskName}`,
        messages[eventType] || `Schedule update: ${taskName}`);
}

/**
 * Set up event listeners for sidebar navigation
 * Call this from the sidebar/navbar template
 */
function addDashboardActivityNotification(category, title, message) {
    /**
     * Helper to add activity notifications to dashboard
     * @param {string} category - Category (feeder, inventory, temperature, etc.)
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     */
    const timestamp = new Date().toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    
    // Get existing notifications
    let notifications = JSON.parse(localStorage.getItem('dashboardNotifications') || '[]');
    
    // Add new notification
    notifications.unshift({
        id: Date.now(),
        category: category,
        title: title,
        message: message,
        timestamp: timestamp
    });
    
    // Keep only last 20 notifications
    if (notifications.length > 20) {
        notifications = notifications.slice(0, 20);
    }
    
    localStorage.setItem('dashboardNotifications', JSON.stringify(notifications));
    
    // Dispatch custom event
    window.dispatchEvent(new Event('storage'));
    
    // Update dashboard if on the same page
    if (typeof updateNotificationDisplay === 'function') {
        updateNotificationDisplay();
    }
}

function setupNavigationNotifications() {
    // Find all sidebar links with notification capability
    const navLinks = document.querySelectorAll('[data-notify-nav]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const pageTitle = this.getAttribute('data-notify-nav');
            
            // Only notify if navigating to external page (not same page)
            notifyNavigation(pageTitle);
        });
    });
}

/**
 * Shortcut for common notifications
 */
const Notification = {
    success: (msg) => triggerNotification(msg, 'success'),
    info: (msg) => triggerNotification(msg, 'info'),
    warning: (msg) => triggerNotification(msg, 'warning'),
    error: (msg) => triggerNotification(msg, 'error'),
    
    // Common operations
    orderCreated: notifyOrderCreated,
    orderUpdated: notifyOrderUpdated,
    inventoryChanged: notifyInventoryChange,
    feederEvent: notifyFeederEvent,
    feederFeedingStatus: notifyFeederFeedingStatus,
    systemAlert: notifySystemAlert,
    temperatureHumidityStatus: notifyTemperatureHumidityStatus,
    scheduleEvent: notifyScheduleEvent,
    navigate: notifyNavigation
};

// Make it globally available
window.Notification = Notification;
window.setupNavigationNotifications = setupNavigationNotifications;
