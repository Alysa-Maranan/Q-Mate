// Notification Helper Functions

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

function clearAllNotifications() {
    // Clear from notification-system.js
    if (notificationSystem) {
        notificationSystem.notifications = [];
        notificationSystem.saveNotifications(); // Save the cleared state
        notificationSystem.updateNotificationUI();
    }
    
    // Clear the bell notifications completely from storage
    localStorage.removeItem('squifm_notifications');
    localStorage.removeItem('squifm_notifications_seen');
    
    // Re-render to show empty state
    if (typeof renderNotifications === 'function') {
        renderNotifications();
    }
}

function clearNotifications(event) {
    event.stopPropagation();
    
    // Clear from notification-system.js
    if (notificationSystem) {
        notificationSystem.notifications = [];
        notificationSystem.saveNotifications(); // Save the cleared state
        notificationSystem.updateNotificationUI();
    }
    
    // Clear the bell notifications completely from storage
    localStorage.removeItem('squifm_notifications');
    localStorage.removeItem('squifm_notifications_seen');
    
    // Re-render to show empty state
    if (typeof renderNotifications === 'function') {
        renderNotifications();
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const notificationBell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    
    if (dropdown && !notificationBell?.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Hook into order form
document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.querySelector('form[action*="order.store"]');
    
    if (orderForm) {
        // Add submit event listener
        orderForm.addEventListener('submit', function(e) {
            // Let the form submit normally, but show notification
            // Note: This triggers before the actual submission
            const submitBtn = orderForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            }
        });

        // Also handle successful form submission via fetch if you want immediate feedback
        // Or listen for the page redirect after submission
        window.addEventListener('beforeunload', function(e) {
            const orderForm = document.querySelector('form[action*="order.store"]');
            if (orderForm && orderForm.querySelector('button[type="submit"]').disabled) {
                // Form was submitted
                if (notificationSystem) {
                    notificationSystem.order('Your order has been submitted successfully! We will contact you soon.');
                }
            }
        });
    }
});

// Feeder integration
function notifyFeederCommand(status, message = '') {
    if (notificationSystem) {
        const msg = message || `Feeder ${status}`;
        notificationSystem.feeder(msg);
    }
}

function notifyManualFeed(message) {
    if (notificationSystem) {
        notificationSystem.feeder(message || 'Manual feeding completed!');
    }
}

// Temperature & Humidity Integration
function notifyTemperatureAlert(temperature, unit = 'C') {
    if (notificationSystem) {
        const message = `Temperature: ${temperature}°${unit}`;
        notificationSystem.temperature(message);
    }
}

function notifyHumidityAlert(humidity) {
    if (notificationSystem) {
        const message = `Humidity: ${humidity}%`;
        notificationSystem.info(message, null, 'Temperature & Humidity');
    }
}

function notifyEnvironmentAlert(type, value, unit) {
    if (notificationSystem) {
        if (type.toLowerCase() === 'temperature') {
            notifyTemperatureAlert(value, unit);
        } else if (type.toLowerCase() === 'humidity') {
            notifyHumidityAlert(value);
        } else {
            notificationSystem.warning(`${type}: ${value}${unit}`);
        }
    }
}

// Inventory Integration
function notifyInventoryUpdate(product, quantity, status = 'updated') {
    if (notificationSystem) {
        const message = `${product}: ${quantity} ${status}`;
        notificationSystem.inventory(message);
    }
}

function notifyLowInventory(product, quantity) {
    if (notificationSystem) {
        const message = `Low Stock: ${product} (${quantity} left)`;
        notificationSystem.warning(message, null, 'Inventory & Sales');
    }
}

function notifyRestock(product, quantity) {
    if (notificationSystem) {
        const message = `${product} restocked (+${quantity})`;
        notificationSystem.success(message, null, 'Inventory & Sales');
    }
}

// Food Level Integration
let lastFoodLevelState = null;
function notifyFoodLevel(level, percentage) {
    if (!notificationSystem) return;

    // Determine current state
    let currentState = 'normal';
    if (percentage >= 0 && percentage <= 10) {
        currentState = 'critical';
    } else if (percentage >= 20 && percentage <= 30) {
        currentState = 'low';
    }
    // Removed: 'refilled' state (80-100%)

    // Only notify once per state change
    if (currentState === lastFoodLevelState) return;
    lastFoodLevelState = currentState;

    // No notification for normal range (31-79%)
    if (currentState === 'normal') return;

    let title, message, type;

    if (currentState === 'critical') {
        title = 'CRITICAL FOOD LEVEL';
        message = 'Food is almost empty! Please refill immediately.';
        type = 'error';
    } else if (currentState === 'low') {
        title = 'LOW FOOD LEVEL';
        message = 'Food is running low in the feeder. Please refill the food soon.';
        type = 'warning';
    }
    // Removed: 'refilled' state notification

    const fullMessage = `${title}\n${message}\n${currentState.toUpperCase()} — ${percentage}%`;
    notificationSystem.addNotification(fullMessage, type, null, 'Feeder');
}

// Generic error notification
function notifyError(message) {
    if (notificationSystem) {
        notificationSystem.error(message);
    }
}

// Generic success notification
function notifySuccess(message) {
    if (notificationSystem) {
        notificationSystem.success(message);
    }
}

// Generic info notification
function notifyInfo(message) {
    if (notificationSystem) {
        notificationSystem.info(message);
    }
}

// Toast Notification Function - Triggers SquifmBell refresh instead of side toast
function showToast(message, type = 'info') {
    // Instead of showing a side toast, trigger the SquifmBell to refresh
    // This ensures notifications appear in the header bell dropdown
    if (typeof SquifmBell !== 'undefined' && SquifmBell !== null) {
        SquifmBell.loadFeed();
    }
    
    // Also try to refresh the notification-system UI if it exists
    if (typeof notificationSystem !== 'undefined' && notificationSystem !== null) {
        notificationSystem.updateNotificationUI();
    }
}

// Export for use in other scripts
window.notificationHelpers = {
    notifyFeederCommand,
    notifyManualFeed,
    notifyTemperatureAlert,
    notifyHumidityAlert,
    notifyEnvironmentAlert,
    notifyInventoryUpdate,
    notifyLowInventory,
    notifyRestock,
    notifyFoodLevel,
    notifyError,
    notifySuccess,
    notifyInfo,
    toggleNotificationDropdown,
    clearNotifications,
    clearAllNotifications
};

// Also make functions available directly on window for convenience
window.notifyEnvironmentAlert = notifyEnvironmentAlert;
window.notifyTemperatureAlert = notifyTemperatureAlert;
window.notifyHumidityAlert = notifyHumidityAlert;
window.notifyInventoryUpdate = notifyInventoryUpdate;
window.notifyLowInventory = notifyLowInventory;
window.notifyRestock = notifyRestock;
window.notifyFeederCommand = notifyFeederCommand;
window.notifyManualFeed = notifyManualFeed;
