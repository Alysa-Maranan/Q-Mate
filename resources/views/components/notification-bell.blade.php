<!-- Notification Bell Partial -->
<div class="notification-bell-container" id="notification-bell-container">
    <div class="notification-bell" id="notification-bell" onclick="toggleNotificationDropdown()">
        <div class="bell-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="1.8" class="bell-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <span class="notification-badge" id="notification-badge" style="display: none;">0</span>
    </div>

    <!-- Notification Dropdown -->
    <div class="notification-dropdown" id="notification-dropdown">
        <div class="notification-header">
            <span style="display:flex;align-items:center;gap:0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Notifications
            </span>
            <span class="clear-notifications" onclick="clearNotifications(event)">Clear All</span>
        </div>
        <div id="notification-list">
            <div class="notification-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#d7ccc8" stroke-width="1.5" style="margin-bottom:1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p>No notifications yet</p>
                <p style="font-size: 0.75rem; color: #a1887f; margin-top: 0.5rem;">You're all caught up!</p>
            </div>
        </div>
    </div>
</div>

<style>
    .notification-bell-container {
        position: relative;
        display: inline-block;
        z-index: 1000;
    }

    .notification-bell {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bell-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #a1887f;
        background: #fffdfa;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(161,136,127,0.15);
    }
    .notification-bell:hover .bell-circle {
        background: #f0ebe6;
        border-color: #6d4c41;
        box-shadow: 0 4px 12px rgba(161,136,127,0.25);
    }

    .bell-icon {
        animation: bellRing 3s ease-in-out infinite;
    }
    .notification-bell:hover .bell-icon { stroke: #4e342e; }

    @keyframes bellRing {
        0%, 80%, 100% { transform: rotate(0deg); }
        83%  { transform: rotate(-12deg); }
        87%  { transform: rotate(12deg); }
        91%  { transform: rotate(-8deg); }
        95%  { transform: rotate(6deg); }
        98%  { transform: rotate(0deg); }
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        animation: badgePulse 2s infinite;
    }

    @keyframes badgePulse {
        0%, 100% {
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        50% {
            box-shadow: 0 2px 12px rgba(255, 107, 107, 0.5);
        }
    }

    .notification-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 380px;
        max-height: 450px;
        overflow-y: auto;
        background: linear-gradient(135deg, #fffdfa 0%, #f5f0eb 100%);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(109,76,65,0.15), 0 0 1px rgba(109,76,65,0.1);
        border: 2px solid #efebe9;
        z-index: 999;
        display: none;
    }}

    .notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from { 
            opacity: 0; 
            transform: translateY(-15px) scale(0.95);
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1);
        }
    }

    .notification-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #efebe9 0%, #e8ddd8 100%);
        border-bottom: 2px solid #d7ccc8;
        border-radius: 14px 14px 0 0;
        font-weight: 700;
        color: #6d4c41;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
    }

    .notification-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #efebe9;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all 0.2s ease;
        position: relative;
        background: #fffdfa;
    }

    .notification-item:hover {
        background: #fdfcfb;
        padding-left: 1.75rem;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.success {
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, transparent 100%);
    }

    .notification-item.info {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(135deg, rgba(59,130,246,0.05) 0%, transparent 100%);
    }

    .notification-item.warning {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, rgba(245,158,11,0.05) 0%, transparent 100%);
    }

    .notification-item.error {
        border-left: 4px solid #ef4444;
        background: linear-gradient(135deg, rgba(239,68,68,0.05) 0%, transparent 100%);
    }

    .notification-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f0ebe6;
    }

    .notification-item.success .notification-icon {
        background: rgba(16,185,129,0.1);
    }

    .notification-item.info .notification-icon {
        background: rgba(59,130,246,0.1);
    }

    .notification-item.warning .notification-icon {
        background: rgba(245,158,11,0.1);
    }

    .notification-item.error .notification-icon {
        background: rgba(239,68,68,0.1);
    }

    .notification-content {
        flex: 1;
    }

    .notification-text {
        font-size: 0.9rem;
        color: #4e342e;
        font-weight: 500;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #a1887f;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .notification-empty {
        padding: 3rem 2rem;
        text-align: center;
        color: #8d6e63;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .notification-empty p {
        margin: 0;
        color: #6d4c41;
        font-weight: 600;
    }

    .clear-notifications {
        font-size: 0.8rem;
        color: #ef4444;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .clear-notifications:hover {
        background: rgba(239,68,68,0.1);
    }

    /* Scrollbar styling */
    .notification-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .notification-dropdown::-webkit-scrollbar-track {
        background: #f0ebe6;
        border-radius: 10px;
    }

    .notification-dropdown::-webkit-scrollbar-thumb {
        background: #d7ccc8;
        border-radius: 10px;
    }

    .notification-dropdown::-webkit-scrollbar-thumb:hover {
        background: #a1887f;
    }
</style>
