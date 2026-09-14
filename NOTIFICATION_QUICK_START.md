# Notification System - Quick Start Examples

## 📌 Basic Usage

### In Browser Console (for testing)
```javascript
// Test notification sounds and animations
notify.success('Order placed!', '✓');
notify.warning('Low stock!', '⚠️');
notify.error('Connection failed', '✕');
```

## 🛒 Order Form Integration

### How It Works
When a user submits an order form, the order.blade.php automatically:
1. Plays notification sound
2. Flashes bell icon
3. Shows "Order received!" notification
4. Auto-dismisses after 5 seconds

### No code changes needed - it's automatic!

## 🍽️ Feeder Notifications

### Manual Trigger
```javascript
// In feeder/schedules.blade.php - already integrated
notify.feeder('Feeder triggered for 5s', '🍽️');
```

### Custom Feeder Messages
```javascript
// From any JavaScript context
window.notify.feeder('Feeder refilled', '🍽️');
window.notify.feeder('Feeding scheduled for 10 AM', '⏰');
window.notify.error('Feeder jam detected', '🚨');
```

## 🌡️ Temperature & Humidity

### Automatic Updates
```javascript
// In temperature-humidity.blade.php - already integrated
// Every 5 seconds automatically:
notify.temperature('🌡️ Temperature: 22°C', '🌡️');
notify.info('💧 Humidity: 65%', '💧');
```

### Alert on Abnormal Readings
```javascript
if (temperature > 28) {
    notify.warning('⚠️ Temperature too high: ' + temperature + '°C', '🔥');
}
if (humidity < 30) {
    notify.warning('💧 Humidity too low: ' + humidity + '%', '💧');
}
```

## 📦 Inventory Notifications

### Stock Updates
```javascript
// In inventory.blade.php - already integrated
notify.inventory('📦 100 eggs added to stock', '📦');
notify.success('✓ Stock updated successfully', '📦');
notify.warning('⚠️ Stock running low: 50 eggs remaining', '📦');
```

### Restock Alerts
```javascript
if (quantity < minThreshold) {
    notify.warning(`Low stock: ${product} (${quantity} left)`, '📦');
}
```

## ✅ Success Notifications

### Generic Success
```javascript
notify.success('Operation completed successfully!', '✓');
```

### Specific Successes
```javascript
notify.success('Order confirmed', '🛒');
notify.success('Feeder refilled', '🍽️');
notify.success('Settings saved', '⚙️');
notify.success('Data exported', '📥');
```

## ⚠️ Warning Notifications

### Common Warnings
```javascript
notify.warning('Connection unstable', '⚠️');
notify.warning('Please check settings', '⚙️');
notify.warning('Low battery on device', '🔋');
notify.warning('Temperature alert', '🌡️');
```

## ❌ Error Notifications

### Error Handling
```javascript
fetch('/api/feeder/trigger', {method: 'POST'})
    .then(r => r.json())
    .catch(err => {
        notify.error('Failed to trigger feeder', '✕');
    });
```

### Specific Errors
```javascript
notify.error('Network connection failed', '✕');
notify.error('Device not responding', '❌');
notify.error('Invalid configuration', '⚙️');
```

## 🔔 Generic Info

```javascript
notify.info('Schedule confirmed for 10 AM', 'ℹ️');
notify.info('Processing your request...', '⏳');
notify.info('Updates available', '📢');
```

## 🔥 Real-World Implementation Examples

### Example 1: Form Submission with Notification
```html
<form onsubmit="handleOrderSubmit(event, this)">
    <input name="product">
    <input name="quantity">
    <button type="submit">Place Order</button>
</form>

<script>
async function handleOrderSubmit(event, form) {
    event.preventDefault();
    
    const data = new FormData(form);
    
    try {
        const response = await fetch('/api/order', {
            method: 'POST',
            body: data
        });
        
        if (response.ok) {
            notify.order('Order placed successfully!', '🛒');
            form.reset();
        } else {
            notify.error('Failed to place order', '✕');
        }
    } catch (err) {
        notify.error('Connection error: ' + err.message, '✕');
    }
}
</script>
```

### Example 2: Periodic Updates with Notification
```javascript
// Check inventory every 30 seconds
setInterval(() => {
    fetch('/api/inventory/current')
        .then(r => r.json())
        .then(data => {
            if (data.low_stock_items.length > 0) {
                notify.warning(
                    `${data.low_stock_items.length} items low on stock`,
                    '📦'
                );
            }
        });
}, 30000);
```

### Example 3: Device Integration Notification
```javascript
// When device sends data
function onDeviceData(temp, humidity) {
    // Update display
    document.getElementById('temp').textContent = temp;
    document.getElementById('humidity').textContent = humidity;
    
    // Notify if abnormal
    if (temp > 28 || temp < 18) {
        notify.warning(`Temperature out of range: ${temp}°C`, '🌡️');
    }
    
    if (humidity > 75 || humidity < 40) {
        notify.warning(`Humidity out of range: ${humidity}%`, '💧');
    }
}
```

### Example 4: Feeder Command with Notification
```javascript
// Button click to trigger feeder
document.getElementById('feed-btn').addEventListener('click', async () => {
    notify.info('Activating feeder...', '⏳');
    
    try {
        const response = await fetch('/feeder/trigger', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({duration: 5})
        });
        
        if (response.ok) {
            notify.feeder('Feeder activated for 5 seconds', '🍽️');
        } else {
            notify.error('Feeder activation failed', '❌');
        }
    } catch (err) {
        notify.error('Failed to reach feeder', '❌');
    }
});
```

## 🎨 Notification Types Quick Reference

| Notification | Code | Sound | Flash | Auto-Dismiss |
|---|---|---|---|---|
| Order Placed | `notify.order(msg, '🛒')` | ✅ | ✅ | ✅ 5s |
| Feeder Active | `notify.feeder(msg, '🍽️')` | ✅ | ✅ | ✅ 5s |
| Temp Update | `notify.temperature(msg, '🌡️')` | ✅ | ✅ | ✅ 5s |
| Stock Update | `notify.inventory(msg, '📦')` | ✅ | ✅ | ✅ 5s |
| Success | `notify.success(msg, '✓')` | ✅ | ✅ | ✅ 5s |
| Info | `notify.info(msg, 'ℹ️')` | ✅ | ✅ | ✅ 5s |
| Warning | `notify.warning(msg, '⚠️')` | ✅ | ✅ | ✅ 5s |
| Error | `notify.error(msg, '✕')` | ✅ | ✅ | ✅ 5s |

## 📱 Mobile Testing

The notification system works perfectly on mobile:
- Bell icon appears in top navigation
- Dropdown displays cleanly
- Sound plays on notification
- Touch-friendly size (44 × 44px)
- Responsive layout

Test on mobile:
1. Open website on phone
2. Submit order form or trigger feeder
3. Listen for notification sound
4. See bell flash
5. Observe notification in dropdown
6. Watch auto-dismiss after 5 seconds

## 🐛 Troubleshooting

### Sound Not Playing?
```javascript
// Check if audio context is available
console.log(window.AudioContext || window.webkitAudioContext);

// Test sound directly
if (notificationSystem) {
    notificationSystem.playNotificationSound();
}
```

### Notification Not Appearing?
```javascript
// Check if system initialized
console.log(notificationSystem);

// Manually verify
if (window.notify) {
    window.notify.success('Test', '✓');
}
```

### Z-Index Issues?
The bell dropdown has `z-index: 1000` in CSS. If it's behind other elements:
```css
/* In your page CSS if needed */
.notification-dropdown {
    z-index: 9999 !important;
}
```

## 🚀 Pro Tips

1. **Batch Similar Notifications**
   ```javascript
   // Don't do this:
   notify.info('Item 1 added', '✓');
   notify.info('Item 2 added', '✓');
   notify.info('Item 3 added', '✓');
   
   // Do this instead:
   notify.success('3 items added to order', '✓');
   ```

2. **Use Appropriate Types**
   ```javascript
   // Errors should use notify.error()
   notify.error('Out of stock', '❌');
   
   // Not:
   notify.warning('Out of stock', '❌');
   ```

3. **Provide Context**
   ```javascript
   // Good:
   notify.warning('Feeder jam detected at 10:30 AM', '🔧');
   
   // Less helpful:
   notify.warning('Feeder error', '❌');
   ```

4. **Test in Console**
   ```javascript
   // Before deploying, always test:
   for(let i = 0; i < 5; i++) {
       setTimeout(() => notify.success(`Test ${i+1}`, '✓'), i * 1000);
   }
   ```

## 📞 Support

For issues or questions about the notification system, check:
1. Browser console for errors
2. Network tab to see if scripts loaded
3. NOTIFICATION_SYSTEM_GUIDE.md for detailed docs
4. This file for quick examples

---

**Last Updated**: Today
**Status**: ✅ Production Ready
