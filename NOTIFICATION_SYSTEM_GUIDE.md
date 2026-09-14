# Notification System Integration Guide

## Overview

The SQUIFM notification system provides a centralized bell notification hub that aggregates notifications from all parts of the application. All notifications are displayed in a unified dropdown with:

- ✅ Sound effects on each notification
- ✅ Visual flash animation on the bell icon  
- ✅ Auto-dismiss after 5 seconds
- ✅ Notification count badge
- ✅ Different notification types (success, info, warning, error)
- ✅ Custom icons for each notification type

## Files

1. **notification-system.js** - Core notification system class and initialization
2. **notification-styles.css** - CSS styling for notifications
3. **notification-helpers.js** - Helper functions and API wrapper
4. **notification-bell.blade.php** - Bell UI component (already integrated in layouts)

## How to Use

### From JavaScript/Frontend

The notification system is accessible via `window.notify` global object:

```javascript
// Simple notifications
notify.success('Order placed successfully!', '✓');
notify.info('Processing your request...', 'ℹ️');
notify.warning('Please check the temperature', '⚠️');
notify.error('Connection failed', '✕');

// Specialized notifications
notify.order('Your order has been submitted!', '🛒');
notify.feeder('Feeder refilled', '🍽️');
notify.temperature('Temperature: 22°C', '🌡️');
notify.inventory('Stock updated', '📦');
```

### Integration Points

#### 1. Order Form (order.blade.php)

When a user submits an order, a notification is automatically triggered:

```javascript
// Already integrated - form submission triggers:
notify.order('Your order has been submitted successfully!', '🛒');
```

#### 2. Temperature & Humidity (temperature-humidity.blade.php)

Temperature and humidity readings automatically notify:

```javascript
// Automatically called every 5 seconds:
checkTemperatureAlerts(temperature);    // Sends: "🌡️ Temperature: 22°C"
checkHumidityAlerts(humidity);          // Sends: "💧 Humidity: 65%"
```

#### 3. Inventory (inventory.blade.php)

Inventory activities trigger notifications:

```javascript
// Use existing addNotification function - automatically integrates:
addNotification('📦', 'Stock updated', 'success');
addNotification('🚨', 'Low stock alert', 'warning');
```

#### 4. Feeder Operations

To add feeder notifications:

```javascript
// Use these helpers:
notify.feeder('Manual feed executed', '🍽️');
notify.feeder('Feeder refilled successfully', '✓');
```

## Notification Types & Icons

| Type | Icon | Usage |
|------|------|-------|
| success | ✓ | Operation completed |
| info | ℹ️ | General information |
| warning | ⚠️ | Alerts, low stock |
| error | ✕ | Failed operations |
| order | 🛒 | Order placed |
| feeder | 🍽️ | Feeder operations |
| temperature | 🌡️ | Temperature updates |
| humidity | 💧 | Humidity updates |
| inventory | 📦 | Stock changes |

## How Notifications Work

### Flow

1. **Trigger** - Any action on the website (form submit, data update, etc.)
2. **Sound** - Web Audio API plays a pleasant 2-tone notification sound
3. **Flash** - Bell icon scales up/down for visual attention
4. **Display** - Notification appears in bell dropdown
5. **Badge** - Red badge shows count of notifications
6. **Auto-dismiss** - After 5 seconds, notification is removed

### Auto-Dismiss Logic

Each notification automatically:
- Displays for 5 seconds
- Plays sound on arrival
- Triggers flash effect
- Shows in dropdown list
- Removes itself after 5 seconds
- Updates badge count

## Example Implementation

### In a Custom Page

```blade
@extends('layouts.app')

@section('content')

<div id="my-form">
    <form onsubmit="handleSubmit(event)">
        <input type="text" name="data">
        <button type="submit">Submit</button>
    </form>
</div>

<script>
function handleSubmit(event) {
    event.preventDefault();
    
    // Your form processing here...
    
    // Show notification
    if (window.notify) {
        notify.success('Data saved successfully!', '✓');
    }
}
</script>

@endsection
```

### In API Response Handler

```javascript
fetch('/api/inventory/update', {
    method: 'POST',
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(data => {
    if (window.notify) {
        notify.success(`${data.product} updated to ${data.quantity}`, '📦');
    }
})
.catch(error => {
    if (window.notify) {
        notify.error('Failed to update inventory', '✕');
    }
});
```

## Notification Bell Component

The notification bell component is located in:
- **Component**: `resources/views/components/notification-bell.blade.php`
- **Included in**: `resources/views/layouts/app.blade.php` (main layout)
- **Public Pages**: `resources/views/welcome2.blade.php`, `resources/views/order.blade.php`

### Features

- **Design**: Premium claymorphism with glossy button and metallic bell
- **Size**: Scales responsively (44×44px main, 36×36px header, 80×80px preview)
- **Animation**: 
  - Bell ring effect on notifications
  - Motion line animations
  - Smooth dropdown transition
  - Badge pulse animation

## Browser Compatibility

- ✅ Chrome/Edge (full support)
- ✅ Firefox (full support)
- ✅ Safari (full support)
- ⚠️ IE 11 (Web Audio API limited)

## Customization

### Custom Notification

```javascript
// Add custom notification
notificationSystem.addNotification(
    message = 'Your custom message',
    type = 'info',     // 'success', 'info', 'warning', 'error'
    icon = '🎉'
);
```

### Change Auto-Dismiss Time

Edit `notification-system.js`, line 96:
```javascript
// Change 5000 to desired milliseconds
setTimeout(() => {
    this.removeNotification(notification.id);
}, 5000);  // <- Change this value
```

### Change Sound

The notification sound is generated by Web Audio API in `playNotificationSound()`. Frequencies can be customized:
- First beep: 800 Hz
- Second beep: 1000 Hz

## Troubleshooting

**Sound not playing?**
- Browser audio context needs user interaction first
- Check browser console for errors
- Some browsers require HTTPS

**Notifications not appearing?**
- Verify `notification-system.js` is loaded
- Check if `notificationSystem` is initialized
- Look for errors in browser console

**Dropdown not opening?**
- Verify bell icon has `id="notification-bell"`
- Check if z-index is being overridden by other CSS

## Testing

Open browser console and run:

```javascript
// Test all notification types
notify.success('Success notification!', '✓');
notify.info('Info notification!', 'ℹ️');
notify.warning('Warning notification!', '⚠️');
notify.error('Error notification!', '✕');

// Test specialized notifications
notify.order('Test order', '🛒');
notify.feeder('Test feeder', '🍽️');
notify.temperature('Test temp', '🌡️');
```

All notifications should:
1. Play a sound
2. Flash the bell
3. Show in dropdown
4. Update badge count
5. Auto-disappear after 5 seconds
