@extends('layouts.app')

@section('content')
@include('components.sidebar')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Font Family - Main */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Sidebar content wrapper - matching dashboard/settings behavior */
    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }
    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }

    /* Expanded state - Full width content */
    .sidebar-content-wrap.expanded .chat-container {
        max-width: 1600px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
        .chat-container { padding: 1rem; }
    }

    /* Page Header - matching settings page style */
    .page-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        text-align: center;
    }
    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }
    .page-title svg {
        color: #6d4c41;
        flex-shrink: 0;
    }
    .page-subtitle {
        color: #8d6e63;
        font-size: 0.95rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
</style>

@push('scripts')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
@endpush

<div id="main-content-wrap" class="sidebar-content-wrap">
    <div class="container" style="padding: 2rem;">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Customer Chat</h1>
            <p class="page-subtitle">Manage your farm conversations and support your customers</p>
        </div>

        <div class="chat-container">
            <style>
            /* * { box-sizing: border-box; margin: 0; padding: 0; } Removed to not break global styles */
            .chat-container {
                background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
                min-height: calc(100vh - 200px);
                padding: 20px;
                border-radius: 20px;
                box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
            }
            .chat-layout {
            display: flex;
            height: calc(100vh - 140px);
            margin-top: 20px;
            gap: 20px;
        }

        .customer-list {
            width: 320px;
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(109,76,65,0.08);
            border: 2px solid #d7ccc8;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .customer-list-header {
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            color: #6d4c41;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            border-bottom: 2px solid #d7ccc8;
        }

        .customer-list-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .customer-list-header h2 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .unread-badge {
            background: #ff6b6b;
            color: white;
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .customer-search {
            position: relative;
        }

        .customer-search input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #a1887f;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            background: #fffdfa;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(141,110,99,0.10);
        }

        .customer-search input:focus {
            border-color: #6d4c41;
            box-shadow: 0 6px 16px rgba(141,110,99,0.18);
        }

        .customer-search i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a1887f;
        }

        .customer-items {
            flex: 1;
            overflow-y: auto;
        }

        .customer-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #efebe9;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 1rem;
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        }

        .customer-item:hover {
            background: #f5eee6;
            box-shadow: inset 0 0 10px rgba(141, 110, 99, 0.1);
        }

        .customer-item.active {
            background: #a1887f;
            border-left: 3px solid #6d4c41;
        }

        .customer-item.active .customer-name,
        .customer-item.active .customer-preview {
            color: white;
        }

        .customer-item.unread {
            background: #fff8e1;
        }

        .customer-item.unread .customer-name {
            font-weight: 700;
        }

        .customer-item.unread .unread-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #2196f3;
            border-radius: 50%;
            margin-left: 0.5rem;
        }

        .online-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #4caf50;
            border-radius: 50%;
            border: 2px solid white;
            position: absolute;
            bottom: 0;
            right: 0;
        }

        .customer-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6d4c41, #8d6e63);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
            position: relative;
            box-shadow: 0 4px 12px rgba(109,76,65,0.2);
        }

        .customer-info {
            flex: 1;
            min-width: 0;
        }

        .customer-name {
            font-weight: 600;
            color: #4e342e;
            margin-bottom: 0.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .customer-time {
            font-size: 0.75rem;
            color: #8d6e63;
            font-weight: 400;
        }

        .customer-preview {
            font-size: 0.85rem;
            color: #8d6e63;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-area {
            flex: 1;
            background: #fffdfa;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(109,76,65,0.08);
            border: 2px solid #d7ccc8;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            color: #6d4c41;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 2px solid #d7ccc8;
        }

        .chat-header-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6d4c41, #8d6e63);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            box-shadow: 0 4px 12px rgba(109,76,65,0.2);
        }

        .chat-header-info h3 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: #4e342e;
        }

        .chat-header-info p {
            font-size: 0.8rem;
            color: #8d6e63;
            margin: 0;
        }

        .chat-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background: linear-gradient(135deg, #faf8f6 0%, #f5f0eb 100%);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            max-width: 70%;
            display: flex;
            flex-direction: column;
        }

        .message-customer {
            align-self: flex-start;
        }

        .message-admin {
            align-self: flex-end;
        }

        .message-bubble {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .message-customer .message-bubble {
            background: white;
            color: #4e342e;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .message-admin .message-bubble {
            background: linear-gradient(135deg, #a1887f 0%, #8d6e63 100%);
            color: white;
        }

        .message-time {
            font-size: 0.7rem;
            color: #8d6e63;
            margin-top: 0.25rem;
            padding: 0 0.5rem;
        }

        .message-customer .message-time {
            text-align: left;
        }

        .message-admin .message-time {
            text-align: right;
        }

        .chat-input {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            border-top: 2px solid #d7ccc8;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .chat-input-wrapper {
            flex: 1;
            background: white;
            border: 2px solid #d7ccc8;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .chat-input-wrapper:focus-within {
            border-color: #a1887f;
            box-shadow: 0 0 0 3px rgba(161, 136, 127, 0.1);
        }

        .chat-input-field {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.95rem;
            color: #4e342e;
            font-family: inherit;
            font-weight: 500;
        }

        .chat-input-field::placeholder {
            color: #a1887f;
        }

        .btn-send {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #a1887f 0%, #8d6e63 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(141, 110, 99, 0.2);
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(141, 110, 99, 0.3);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .empty-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #8d6e63;
            text-align: center;
            padding: 2rem;
        }

        .empty-chat i {
            font-size: 5rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .empty-chat h3 {
            font-size: 1.25rem;
            color: #4e342e;
            margin-bottom: 0.5rem;
        }

        .empty-chat p {
            font-size: 0.9rem;
            max-width: 300px;
        }

        .message-date-separator {
            text-align: center;
            margin: 1rem 0;
            position: relative;
        }

        .message-date-separator span {
            background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 100%);
            padding: 0.25rem 1rem;
            border-radius: 12px;
            font-size: 0.75rem;
            color: #8d6e63;
            border: 1px solid #d7ccc8;
        }

        .quick-replies {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            padding: 0.5rem 1rem;
            border-top: 1px solid #efebe9;
            background: #faf8f6;
        }

        .quick-reply-btn {
            padding: 0.4rem 0.8rem;
            background: white;
            border: 1px solid #d7ccc8;
            border-radius: 16px;
            font-size: 0.8rem;
            color: #6d4c41;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-reply-btn:hover {
            background: #a1887f;
            color: white;
            border-color: #a1887f;
        }

        .chat-header-actions {
            margin-left: auto;
            display: flex;
            gap: 0.5rem;
        }

        .header-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #8d6e63;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .header-action-btn:hover {
            background: #efebe9;
            color: #6d4c41;
        }

        .customer-profile-panel {
            position: fixed;
            right: -350px;
            top: 72px;
            width: 320px;
            height: calc(100vh - 72px);
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            border-left: 2px solid #d7ccc8;
            box-shadow: -4px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: right 0.3s ease;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .customer-profile-panel.open {
            right: 0;
        }

        .profile-close-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: #efebe9;
            color: #6d4c41;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0 auto 1rem;
        }

        .profile-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #4e342e;
            margin-bottom: 0.25rem;
        }

        .profile-email {
            font-size: 0.9rem;
            color: #8d6e63;
        }

        .profile-section {
            margin-bottom: 1.5rem;
        }

        .profile-section h4 {
            font-size: 0.85rem;
            font-weight: 600;
            color: #8d6e63;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #efebe9;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #6d4c41;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #8d6e63;
        }

        .overlay {
            position: fixed;
            top: 72px;
            left: 0;
            width: 100%;
            height: calc(100vh - 72px);
            background: rgba(0,0,0,0.3);
            z-index: 999;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 12px;
            width: fit-content;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #8d6e63;
            border-radius: 50%;
            animation: typing-bounce 1.4s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing-bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .message-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
            margin-top: 0.75rem;
        }

        .message:first-child .message-meta {
            margin-top: 0;
        }

        .admin-avatar-small {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
        }

        .customer-avatar-small {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
        }

        .message-sender {
            font-size: 0.7rem;
            color: #8d6e63;
            font-weight: 500;
        }

        .message-admin .message-sender {
            text-align: right;
        }

        @media (max-width: 768px) {
            .chat-layout {
                flex-direction: column;
                margin: 10px;
                height: calc(100vh - 20px);
            }
            
            .customer-list {
                width: 100%;
                max-height: 200px;
            }
        }
    </style>

    <div class="chat-layout">
        <div class="customer-list">
            <div class="customer-list-header">
                <div class="customer-list-header-top">
                    <h2>Customers</h2>
                    @if($unreadCount > 0)
                        <span class="unread-badge" id="unreadCountBadge">{{ $unreadCount }} unread</span>
                    @else
                        <span class="unread-badge" id="unreadCountBadge" style="display:none;">0 unread</span>
                    @endif
                </div>
                <div class="customer-search">
                    <i class="ph-bold ph-magnifying-glass"></i>
                    <input type="text" id="customerSearch" placeholder="Search customers..." oninput="filterCustomers()">
                </div>
            </div>
            <div class="customer-items" id="customerItems">
                @forelse($customers as $customer)
                    @php
                        $lastChat = $customer->chats->first();
                        $hasUnread = $lastChat && $lastChat->sender === 'customer' && $lastChat->status === 'sent';
                    @endphp
                    <div class="customer-item {{ $hasUnread ? 'unread' : '' }}"
                         onclick="selectCustomer({{ $customer->id }})"
                         data-customer-id="{{ $customer->id }}"
                         data-customer-name="{{ strtolower($customer->name) }}">
                        <div class="customer-avatar">
                            {{ substr($customer->name, 0, 1) }}
                            <span class="online-dot" id="onlineStatus{{ $customer->id }}" title="Online"></span>
                        </div>
                        <div class="customer-info">
                            <div class="customer-name">
                                {{ $customer->name }}
                                @if($hasUnread)
                                    <span class="unread-dot"></span>
                                @endif
                                @if($lastChat)
                                    <span class="customer-time">{{ $lastChat->created_at->diffForHumans() }}</span>
                                @endif
                            </div>
                            <div class="customer-preview">
                                @if($lastChat)
                                    {{ Str::limit($lastChat->message, 40) }}
                                @else
                                    No messages yet
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding: 2rem; text-align: center; color: #8d6e63;">
                        <i class="ph-fill ph-users" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p style="margin-top: 1rem;">No customers yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="chat-area">
            <div class="chat-header" id="chatHeader" style="display: none;">
                <div class="chat-header-avatar" id="chatHeaderAvatar">C</div>
                <div class="chat-header-info">
                    <h3 id="chatHeaderName">Customer Name</h3>
                    <p id="chatHeaderEmail">customer@email.com</p>
                </div>
                <div class="chat-header-actions">
                    <button class="header-action-btn" onclick="toggleCustomerProfile()" title="Customer Info">
                        <i class="ph-bold ph-user"></i>
                    </button>
                    <button class="header-action-btn" onclick="toggleMuteNotifications()" id="muteBtn" title="Mute Notifications">
                        <i class="ph-bold ph-bell"></i>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="empty-chat">
                    <i class="ph-fill ph-chat-circle-text"></i>
                    <h3>Select a Customer</h3>
                    <p>Choose a customer from the list to view and reply to their messages</p>
                </div>
            </div>

            <div class="chat-input" id="chatInput" style="display: none;">
                <div class="quick-replies" id="quickReplies">
                    <button class="quick-reply-btn" onclick="useQuickReply('Hello! How can I help you today?')"><i class="ph-bold ph-hand-waving"></i> Hello</button>
                    <button class="quick-reply-btn" onclick="useQuickReply('Your order is being processed.')"><i class="ph-bold ph-package"></i> Order Status</button>
                    <button class="quick-reply-btn" onclick="useQuickReply('For concerns about your quails, please provide details.')"><i class="ph-bold ph-bird"></i> Quail Help</button>
                    <button class="quick-reply-btn" onclick="useQuickReply('Thank you for contacting us! Have a great day!')"><i class="ph-bold ph-check-circle"></i> Thanks</button>
                </div>
                <div class="chat-input-wrapper" style="gap:0.75rem;">
                    <input type="file" id="imageInput" accept="image/*" style="display:none;" />
                    <button type="button" class="header-action-btn" style="width:auto; padding:0 0.6rem;" onclick="document.getElementById('imageInput').click()" title="Upload image">
                        <i class="ph-bold ph-image"></i>
                    </button>
                    <div id="imagePreviewWrap" style="display:none; align-items:center; gap:0.5rem;">
                        <img id="imagePreview" style="width:52px; height:52px; object-fit:cover; border-radius:10px; border:1px solid #d7ccc8;" />
                        <button type="button" class="header-action-btn" style="width:32px; height:32px;" onclick="clearImage()" title="Remove image">
                            <i class="ph-bold ph-x"></i>
                        </button>
                    </div>
                    <input type="text" id="messageInput" class="chat-input-field" placeholder="Type your reply..." autocomplete="off">
                </div>
                <button type="button" id="sendBtn" class="btn-send" onclick="sendMessage()">
                    <i class="ph-bold ph-paper-plane-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Profile Panel -->
    <div class="overlay" id="profileOverlay" onclick="toggleCustomerProfile()"></div>
    <div class="customer-profile-panel" id="customerProfilePanel">
        <button class="profile-close-btn" onclick="toggleCustomerProfile()">
            <i class="ph-bold ph-x"></i>
        </button>
        <div class="profile-header">
            <div class="profile-avatar" id="profileAvatar">C</div>
            <div class="profile-name" id="profileName">Customer Name</div>
            <div class="profile-email" id="profileEmail">customer@email.com</div>
        </div>
        <div class="profile-section">
            <h4 style="margin-bottom: 0.5rem;"><i class="ph-bold ph-chart-bar" style="margin-right: 0.25rem;"></i> Statistics</h4>
            <div class="profile-stats" style="display: flex; gap: 1rem;">
                <div class="stat-card" style="flex: 1; text-align: center; padding: 0.75rem;">
                    <div class="stat-value" id="statMessages" style="font-size: 1.25rem;">0</div>
                    <div class="stat-label" style="font-size: 0.7rem;">Messages</div>
                </div>
                <div class="stat-card" style="flex: 1; text-align: center; padding: 0.75rem;">
                    <div class="stat-value" id="statDays" style="font-size: 1.25rem;">0</div>
                    <div class="stat-label" style="font-size: 0.7rem;">Days Active</div>
                </div>
            </div>
        </div>
        <div class="profile-section">
            <h4 style="margin-bottom: 0.5rem;"><i class="ph-bold ph-clock" style="margin-right: 0.25rem;"></i> Last Active</h4>
            <p style="color: #4e342e; font-size: 0.9rem; margin: 0;" id="profileLastActive">Just now</p>
        </div>
        <div class="profile-section">
            <h4 style="margin-bottom: 0.75rem;"><i class="ph-bold ph-lightning" style="margin-right: 0.25rem;"></i> Quick Actions</h4>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <button class="btn btn-secondary" style="width: 100%; justify-content: center; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem;" onclick="sendQuickTemplate('order_status')">
                    <i class="ph-bold ph-package"></i> Send Order Status
                </button>
                <button class="btn btn-secondary" style="width: 100%; justify-content: center; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem;" onclick="sendQuickTemplate('feeding_guide')">
                    <i class="ph-bold ph-bird"></i> Send Feeding Guide
                </button>
                <button class="btn btn-secondary" style="width: 100%; justify-content: center; display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem;" onclick="sendQuickTemplate('contact_info')">
                    <i class="ph-bold ph-phone"></i> Send Contact Info
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
        let currentCustomerId = null;

        async function selectCustomer(customerId) {
            currentCustomerId = customerId;
            
            document.querySelectorAll('.customer-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-customer-id="${customerId}"]`).classList.add('active');

            document.getElementById('chatHeader').style.display = 'flex';
            document.getElementById('chatInput').style.display = 'flex';

            try {
                const response = await fetch(`/admin/chat/customer/${customerId}`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('chatHeaderAvatar').textContent = data.customer.name.charAt(0);
                    document.getElementById('chatHeaderName').textContent = data.customer.name;
                    document.getElementById('chatHeaderEmail').textContent = data.customer.email;
                    renderMessages(data.chats, data.customer.name);
                    updateCustomerProfile(currentCustomerId, {
                        name: data.customer.name,
                        email: data.customer.email,
                        messageCount: data.customer.messageCount || data.chats.length,
                        daysActive: data.customer.daysActive || 1,
                        lastActive: data.chats.length > 0 ? formatTimeAgo(data.chats[0].created_at) : 'Just now'
                    });
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }

        function renderMessages(chats, customerName) {
            const container = document.getElementById('chatMessages');
            const customerInitial = customerName ? customerName.charAt(0) : 'C';

            if (chats.length === 0) {
                container.innerHTML = `
                    <div class="empty-chat">
                        <i class="ph-fill ph-chat-circle-dots"></i>
                        <h3>No Messages Yet</h3>
                        <p>This customer hasn't sent any messages yet. Start the conversation by sending a message below!</p>
                    </div>
                `;
                return;
            }

            let html = '';
            let lastDate = null;

            chats.forEach(chat => {
                const chatDate = new Date(chat.created_at).toDateString();

                if (chatDate !== lastDate) {
                    html += `
                        <div class="message-date-separator">
                            <span>${formatDateSeparator(chat.created_at)}</span>
                        </div>
                    `;
                    lastDate = chatDate;
                }

                const messageText = chat.message ? escapeHtml(chat.message) : '';
                const imageHtml = chat.image_path
                    ? `<div style="margin-bottom:0.5rem;"><img src="/storage/${chat.image_path}" style="max-width:100%; height:auto; border-radius:12px; border:1px solid rgba(255,255,255,0.25);"></div>`
                    : '';

                html += `
                    <div class="message message-${chat.sender}">
                        <div class="message-meta">
                            ${chat.sender === 'admin'
                                ? '<div class="admin-avatar-small">A</div><span class="message-sender">Admin</span>'
                                : '<div class="customer-avatar-small">' + customerInitial + '</div><span class="message-sender">' + customerName + '</span>'
                            }
                        </div>
                        <div class="message-bubble">${imageHtml}${messageText}</div>
                        <div class="message-time">${formatTimeAgo(chat.created_at)}</div>
                    </div>
                `;
            });

            container.innerHTML = html;
            scrollToBottom();
        }

        function formatDateSeparator(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            if (date.toDateString() === today.toDateString()) {
                return 'Today';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
        }

        function scrollToBottom() {
            const container = document.getElementById('chatMessages');
            container.scrollTop = container.scrollHeight;
        }

        function clearImage() {
            const fileInput = document.getElementById('imageInput');
            fileInput.value = '';
            document.getElementById('imagePreviewWrap').style.display = 'none';
            document.getElementById('imagePreview').src = '';
        }

        document.getElementById('imageInput').addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                clearImage();
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewWrap').style.display = 'flex';
            };
            reader.readAsDataURL(file);
        });

        async function sendMessage() {
            if (!currentCustomerId) return;

            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            const imageFile = document.getElementById('imageInput').files[0];

            if (!message && !imageFile) return;

            input.value = '';
            clearImage();
            document.getElementById('sendBtn').disabled = true;

            try {
                const formData = new FormData();
                formData.append('customer_id', currentCustomerId);
                if (message) formData.append('message', message);
                if (imageFile) formData.append('image', imageFile);

                const response = await fetch('{{ route('admin.chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    selectCustomer(currentCustomerId);
                } else {
                    console.error('Send failed:', data);
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }

            document.getElementById('sendBtn').disabled = false;
        }

        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        setInterval(() => {
            if (currentCustomerId) {
                selectCustomer(currentCustomerId);
            }
        }, 5000);

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        }

        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return diffMins + 'm ago';
            if (diffHours < 24) return diffHours + 'h ago';
            if (diffDays < 7) return diffDays + 'd ago';
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }

        function filterCustomers() {
            const searchInput = document.getElementById('customerSearch').value.toLowerCase();
            const customerItems = document.querySelectorAll('.customer-item');
            customerItems.forEach(item => {
                const name = item.getAttribute('data-customer-name');
                if (name.includes(searchInput)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
            // Show "no results" message if all hidden
            const visibleCount = [...customerItems].filter(item => item.style.display !== 'none').length;
            const noResultsEl = document.getElementById('noSearchResults');
            if (visibleCount === 0 && searchInput.length > 0) {
                if (!noResultsEl) {
                    const msg = document.createElement('div');
                    msg.id = 'noSearchResults';
                    msg.style.cssText = 'padding: 2rem; text-align: center; color: #8d6e63;';
                    msg.innerHTML = '<i class="ph-fill ph-magnifying-glass" style="font-size: 2rem; opacity: 0.3;"></i><p style="margin-top: 1rem;">No customers found</p>';
                    document.getElementById('customerItems').appendChild(msg);
                }
            } else if (noResultsEl) {
                noResultsEl.remove();
            }
        }

        let notificationsMuted = false;

        function toggleMuteNotifications() {
            notificationsMuted = !notificationsMuted;
            const btn = document.getElementById('muteBtn');
            if (notificationsMuted) {
                btn.innerHTML = '<i class="ph-bold ph-bell-slash"></i>';
                btn.style.color = '#ff6b6b';
            } else {
                btn.innerHTML = '<i class="ph-bold ph-bell"></i>';
                btn.style.color = '#8d6e63';
            }
        }

        function toggleCustomerProfile() {
            const panel = document.getElementById('customerProfilePanel');
            const overlay = document.getElementById('profileOverlay');
            panel.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        function useQuickReply(message) {
            document.getElementById('messageInput').value = message;
            document.getElementById('messageInput').focus();
        }

        function sendQuickTemplate(template) {
            const templates = {
                order_status: '[Order Status]\n\nYour order is being processed!\n\nEstimated delivery: 3-5 business days.\n\nTrack your order at: [link]',
                feeding_guide: '[Quail Feeding Guide]\n\n\n• Chicks (0-3 weeks): 20-24% protein starter feed\n• Growers (3-6 weeks): 18-20% protein grower feed\n• Layers (6+ weeks): 18% protein layer feed with calcium\n\nFresh water should always be available!',
                contact_info: '[Contact Information]\n\n• Phone: 0917-123-4567\n• Email: support@quailfarm.ph\n• Hours: Mon-Fri, 8AM-5PM\n\nWe\'re here to help!'
            };
            useQuickReply(templates[template] || 'Template not found');
        }

        function updateCustomerProfile(customerId, customerData) {
            document.getElementById('profileAvatar').textContent = customerData.name.charAt(0);
            document.getElementById('profileName').textContent = customerData.name;
            document.getElementById('profileEmail').textContent = customerData.email;
            document.getElementById('statMessages').textContent = customerData.messageCount || 0;
            document.getElementById('statDays').textContent = customerData.daysActive || 0;
            document.getElementById('profileLastActive').textContent = customerData.lastActive || 'Unknown';
        }

        function showTypingIndicator() {
            const container = document.getElementById('chatMessages');
            const indicator = document.createElement('div');
            indicator.className = 'message message-customer';
            indicator.id = 'typingIndicator';
            indicator.innerHTML = `
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
            container.appendChild(indicator);
            scrollToBottom();
        }

        async function refreshUnreadBadge() {
            try {
                const res = await fetch('{{ route('admin.chat.unread') }}');
                const data = await res.json();

                const badge = document.getElementById('unreadCountBadge');
                if (!badge) return;

                const count = data.unread_count || 0;
                if (count > 0) {
                    badge.style.display = 'inline-block';
                    badge.textContent = count + ' unread';
                } else {
                    badge.style.display = 'none';
                }
            } catch (e) {
                console.log('Error refreshing unread count:', e);
            }
        }

        // Poll unread count every 5s so it updates without refresh
        document.addEventListener('DOMContentLoaded', function() {
            refreshUnreadBadge();
            setInterval(refreshUnreadBadge, 5000);
        });
    </script>
        </div>
    </div>
</div>
@endsection

