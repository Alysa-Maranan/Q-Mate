# Notification System Usage Guide

The comprehensive notification system is now active across SQUIFM. Here's how to use it:

## How the System Works

### 1. **Navigation Notifications** ✅ (Already Integrated)
When users click on sidebar items, notifications automatically trigger:
- Dashboard → "📊 Navigated to Dashboard"
- Feeder System → "🍽️ Navigated to Feeder System"
- Temperature & Humidity → "🌡️ Navigated to Temperature & Humidity"
- Inventory & Sales → "📦 Navigated to Inventory & Sales"
- Learn Book → "📚 Navigated to Learn Book"
- Settings → "⚙️ Navigated to Settings"

Each notification:
- Plays a pleasant notification sound
- Shows a badge count on the bell
- Flashes the bell icon
- Appears in the notification dropdown
- Persists until user clears

### 2. **Order Notifications** (Ready to Integrate)
Add to order form submission:

```javascript
// When a new order is created
notifyOrderCreated(orderId, customerName, totalAmount);
// Example:
notifyOrderCreated('ORD-001', 'Juan Dela Cruz', 2500);

// When order status changes
notifyOrderUpdated(orderId, status);
// Example:
notifyOrderUpdated('ORD-001', 'confirmed');
// Status can be: pending, confirmed, processing, shipped, delivered, cancelled
```

### 3. **Inventory Notifications**

```javascript
// When inventory items change
notifyInventoryChange(action, itemName, quantity);

// Actions: 'added', 'updated', 'low'
notifyInventoryChange('added', 'Quail Eggs', 500);
notifyInventoryChange('updated', 'Feed', 250);
notifyInventoryChange('low', 'Layer Feed', 10);
```

### 4. **Feeder System Notifications**

```javascript
notifyFeederEvent(action, details);

// Actions: 'refill', 'maintenance', 'error'
notifyFeederEvent('refill', 'Hopper refilled successfully');
notifyFeederEvent('maintenance', 'Scheduled for 2026-04-15');
notifyFeederEvent('error', 'Motor jam detected - manual check needed');
```

### 5. **System Alerts**

```javascript
notifySystemAlert(alertType, message);

// Alert types: temperature, humidity, power, maintenance, backup
notifySystemAlert('temperature', '⚠️ Temperature too high: 35°C (normal: 22-28°C)');
notifySystemAlert('humidity', 'Humidity below threshold: 30% (target: 50-60%)');
notifySystemAlert('power', '⚡ Power backup activated - Grid power lost');
```

### 6. **Schedule Notifications**

```javascript
notifyScheduleEvent(eventType, taskName);

// Event types: 'scheduled', 'started', 'completed'
notifyScheduleEvent('scheduled', 'Feeding time: 6:00 AM');
notifyScheduleEvent('started', 'Feeding time: 6:00 AM');
notifyScheduleEvent('completed', 'Feeding time: 6:00 AM');
```

### 7. **Generic/Custom Notifications**

```javascript
// For any custom notification
triggerNotification(message, type, icon);

// Types: 'success', 'info', 'warning', 'error'
triggerNotification('Quail count updated to 500', 'success', '✓');
triggerNotification('System maintenance scheduled', 'warning', '⚠️');

// Or use the shorthand Notification object
Notification.success('Action completed successfully');
Notification.warning('Please check settings');
Notification.error('Failed to save data');
Notification.info('New update available');
```

## Implementation Examples

### In an Order Form

```html
<form onsubmit="handleOrderSubmit(event)">
    <!-- Form fields -->
    <input type="text" id="customer-name" placeholder="Customer Name">
    <input type="number" id="total-amount" placeholder="Total Amount">
    <button type="submit">Create Order</button>
</form>

<script>
function handleOrderSubmit(event) {
    event.preventDefault();
    
    const customername = document.getElementById('customer-name').value;
    const totalAmount = parseFloat(document.getElementById('total-amount').value);
    
    // Your form submission logic here
    fetch('/api/orders', {
        method: 'POST',
        body: JSON.stringify({ customerName, totalAmount }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Trigger notification
            notifyOrderCreated(data.orderId, customerName, totalAmount);
            
            // Clear form
            event.target.reset();
        }
    })
    .catch(error => {
        Notification.error('Failed to create order');
        console.error(error);
    });
}
</script>
```

### In a Feeder Control Panel

```javascript
// When user refills feeder
function refillFeeder() {
    // API call to refill
    fetch('/api/feeder/refill', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            notifyFeederEvent('refill', 'Feeder refilled at ' + new Date().toLocaleTimeString());
        });
}

// When temperature alert occurs
function handleTemperatureAlert(tempValue) {
    if (tempValue > 30) {
        notifySystemAlert('temperature', `⚠️ High temperature detected: ${tempValue}°C`);
    } else if (tempValue < 15) {
        notifySystemAlert('temperature', `❄️ Low temperature detected: ${tempValue}°C`);
    }
}
```

### In Inventory Management

```javascript
// When updating quail count
function updateQuailCount(newCount) {
    const oldCount = parseInt(localStorage.getItem('quailCount') || '0');
    const change = newCount - oldCount;
    
    if (change > 0) {
        notifyInventoryChange('added', 'Quail Birds', change);
    } else if (change < 0) {
        notifyInventoryChange('updated', 'Quail Birds', newCount);
    }
    
    localStorage.setItem('quailCount', newCount);
}
```

## Notification Sound and Visual Effects

Each notification automatically includes:
- **Sound Effect**: Pleasant two-tone notification sound (can be muted by browser)
- **Bell Flash**: Quick visual flash animation on the notification bell
- **Badge Count**: Shows total number of unread notifications
- **Visual Indicator**: Different colors for different notification types
  - ✓ Success: Green
  - ℹ️ Info: Blue  
  - ⚠️ Warning: Orange
  - ✕ Error: Red

## Notification Storage

- Notifications persist in browser's localStorage
- Maximum 10 notifications shown at once
- Older notifications are automatically removed when limit reached
- Users can click "Clear All" to manually clear all notifications
- Cleared notifications data cleared after 1 hour

## Files Modified

1. **public/notification-events.js** (NEW)
   - Global notification event system
   - Provides all trigger functions
   - Exposes `Notification` object for easy access

2. **public/notification-system.js** (EXISTING)
   - Core notification system
   - Sound generation
   - UI update handling
   - localStorage persistence

3. **resources/views/layouts/app.blade.php** (UPDATED)
   - Added notification-events.js script
   - Loaded on all pages

4. **resources/views/components/sidebar.blade.php** (UPDATED)
   - Added onclick handlers to all navigation links
   - Each link triggers `notifyNavigation()`

## Current Status

✅ Navigation notifications working
✅ Sound effects enabled
✅ Bell flashes on new notifications
✅ Badge count updates
✅ Notification persistence

## Ready to Integrate

The system is ready for:
- [ ] Order form notifications
- [ ] Inventory change notifications
- [ ] Feeder system alerts
- [ ] Temperature/humidity warnings
- [ ] Schedule notifications
- [ ] Custom business logic notifications

Simply call the appropriate function when the event occurs!
