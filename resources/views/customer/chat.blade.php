@extends('customer.layout')


@section('styles')
<style>
    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 100px);
    }
    .page-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        text-align: center;
        color: #6d4c41;
    }
    .page-title {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    .page-subtitle {
        color: #8d6e63;
        font-size: 0.95rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    .page-title i { font-size: 1.8rem; }
    .subtitle { color: rgba(255,255,255,0.9); font-size: 0.9rem; margin: 0; }
    .section-card {
        background: white;
        border-radius: 16px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(109,76,65,0.08);
        border: 1px solid rgba(161,136,127,0.2);
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }
    .chat-header {
        background: linear-gradient(135deg, #f8f5f1 0%, #efebe9 100%);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(161,136,127,0.15);
    }
    .chat-header-left { display: flex; align-items: center; gap: 1rem; }
    .chat-header-icon {
        width: 54px; height: 54px;
        background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.5rem;
    }
    .chat-header-info h3 { font-size: 1.2rem; font-weight: 700; color: #4e342e; margin: 0; }
    .chat-header-info p { font-size: 0.85rem; color: #8d6e63; margin: 0.25rem 0 0 0; }
    .chat-status { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #26aa99; }
    .chat-status::before { content: ''; width: 8px; height: 8px; background: #26aa99; border-radius: 50%; animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
    .chat-messages { flex: 1; padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; background: #fafafa; scroll-behavior: smooth; min-height: 300px; }
    .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-messages::-webkit-scrollbar-track { background: #f0f0f0; }
    .chat-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
    .chat-messages::-webkit-scrollbar-thumb:hover { background: #999; }
    .message { display: flex; flex-direction: column; gap: 0.25rem; max-width: 80%; }
    .message-customer { align-self: flex-end; }
    .message-admin { align-self: flex-start; }
    .message-content { display: flex; align-items: flex-end; gap: 0.5rem; }
    .message-admin .message-content { flex-direction: row; }
    .message-customer .message-content { flex-direction: row-reverse; }
    .message-bubble { padding: 0.85rem 1.2rem; border-radius: 12px; font-size: 0.9rem; line-height: 1.5; word-wrap: break-word; }
    .message-customer .message-bubble { background: #6d4c41; color: white; box-shadow: 0 2px 8px rgba(109,76,65,0.15); }
    .message-admin .message-bubble { background: white; color: #4e342e; border: 1px solid rgba(161,136,127,0.2); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .message-time { font-size: 0.75rem; color: #a1887f; padding: 0 0.5rem; }
    .message-customer .message-time { text-align: right; }
    .message-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; }
    .message-admin .message-avatar { background: #6d4c41; color: white; }
    .date-separator { display: flex; align-items: center; justify-content: center; margin: 1rem 0; }
    .date-separator span { background: #e0e0e0; color: #757575; font-size: 0.75rem; padding: 0.25rem 1rem; border-radius: 12px; }
    .empty-chat { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #8d6e63; text-align: center; padding: 2rem; }
    .empty-chat i { font-size: 5rem; color: #e0e0e0; margin-bottom: 1.5rem; }
    .empty-chat h3 { font-size: 1.3rem; color: #4e342e; margin: 0 0 0.5rem 0; font-weight: 700; }
    .empty-chat p { font-size: 0.95rem; color: #8d6e63; max-width: 300px; }
    .typing-indicator { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; color: #8d6e63; font-size: 0.85rem; }
    .typing-dots { display: flex; gap: 3px; }
    .typing-dots span { width: 6px; height: 6px; background: #a1887f; border-radius: 50%; animation: typing 1.4s infinite; }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing { 0%,100% { opacity: 0.4; transform: translateY(0); } 50% { opacity: 1; transform: translateY(-3px); } }
    .quick-replies { padding: 0.75rem 1.5rem; background: #fafafa; border-top: 1px solid rgba(161,136,127,0.1); display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .quick-reply-btn { padding: 0.4rem 0.8rem; background: white; border: 1px solid #d7ccc8; border-radius: 16px; font-size: 0.8rem; color: #6d4c41; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.3rem; }
    .quick-reply-btn:hover { background: #6d4c41; color: white; border-color: #6d4c41; }
    .chat-input-section { padding: 1.25rem 1.5rem; background: white; border-top: 1px solid rgba(161,136,127,0.15); display: flex; gap: 0.75rem; align-items: flex-end; }
    .message-status { display: flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; color: #a1887f; }
    .message-status.seen { color: #26a99a; }
    .message-status.seen i { color: #26a99a; }
    .attach-btn { width: 44px; height: 44px; background: transparent; border: none; color: #a1887f; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; transition: color 0.2s; }
    .attach-btn:hover { color: #6d4c41; }
    .file-preview { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: #f5f0eb; border-radius: 8px; margin-bottom: 0.5rem; }
    .file-preview img { max-width: 100px; max-height: 100px; border-radius: 4px; }
    .file-preview .file-name { flex: 1; font-size: 0.85rem; color: #4e342e; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-preview .remove-file { background: none; border: none; color: #a1887f; cursor: pointer; font-size: 1rem; }
    .file-preview .remove-file:hover { color: #ff6b6b; }
    .chat-input-wrapper { flex: 1; background: #faf8f6; border: 1px solid #e0e0e0; border-radius: 8px; padding: 0 1rem; display: flex; align-items: center; transition: all 0.2s; }
    .chat-input-wrapper:focus-within { border-color: #a1887f; box-shadow: 0 0 0 3px rgba(161,136,127,0.1); background: white; }
    .chat-input-field { flex: 1; border: none; outline: none; background: transparent; font-size: 0.9rem; color: #4e342e; font-family: inherit; padding: 0.75rem 0; resize: none; max-height: 100px; font-weight: 500; }
    .chat-input-field::placeholder { color: #a1887f; }
    .btn-send { width: 44px; height: 44px; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border: none; border-radius: 8px; color: white; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 4px 12px rgba(109,76,65,0.3); flex-shrink: 0; }
    .btn-send:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109,76,65,0.3); }
    .btn-send:active:not(:disabled) { transform: translateY(0); }
    .btn-send:disabled { opacity: 0.5; cursor: not-allowed; }
    @media (max-width: 768px) {
        .container { padding: 1rem; }
        .page-header { padding: 1.5rem; margin-bottom: 1.5rem; }
        .page-title { font-size: 1.5rem; }
        .chat-messages { min-height: 300px; }
        .message { max-width: 85%; }
    }
</style>
@endsection

@section('customer-content')
<div class="container">
    @php
        $customerId = auth('customer')->id();
        $totalOrders = \App\Models\Order::where('customer_id', $customerId)->count();
        $ordersThisMonth = \App\Models\Order::where('customer_id', $customerId)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    @endphp

    <div class="page-header">
        <h1 class="page-title">Chat With Us</h1>
        <div class="page-subtitle" style="display:flex; gap:1.5rem; justify-content:center; flex-wrap:wrap;">
            <span><i class="" style="margin-right: 0.25rem;"></i> Got questions about your order or our services? Drop us a message below.</span>
        </div>
    </div>


    <div class="section-card">
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-header-icon"><i class="ph-fill ph-headset"></i></div>
                <div class="chat-header-info">
                    <h3>Customer Support Team</h3>
                    <p>We typically reply within a few minutes</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button onclick="toggleChatMute(event)" id="chatMuteBtn" style="background: transparent; border: none; cursor: pointer; position: relative; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='#fcfafa';" onmouseout="this.style.background='transparent';">
                    <i id="chatMuteIcon" class="ph-bold ph-bell" style="font-size: 1.6rem; color: #6d4c41; filter: drop-shadow(0 2px 4px rgba(109,76,65,0.2)); transition: all 0.2s;"></i>
                </button>
                <div class="chat-status">Online</div>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            @if($chats->count() > 0)
                @foreach($chats as $chat)
                    @if($loop->first || $chats[$loop->index-1]->created_at->format('Y-m-d') !== $chat->created_at->format('Y-m-d'))
                    <div class="date-separator"><span>{{ $chat->created_at->format('M d, Y') }}</span></div>
                    @endif
                    <div class="message message-{{ $chat->sender }}">
                        <div class="message-content">
                            @if($chat->sender == 'admin')<div class="message-avatar">S</div>@endif
                            <div class="message-bubble">
                                @if(!empty($chat->image_path))
                                    <div style="margin-bottom:0.5rem;">
                                        <img src="{{ asset('storage/' . $chat->image_path) }}" style="max-width: 100%; height:auto; border-radius:12px; border:1px solid rgba(255,255,255,0.25);">
                                    </div>
                                @endif
                                {{ $chat->message }}
                            </div>
                        </div>
                        <div class="message-time">{{ $chat->created_at->format('h:i A') }}</div>
                    </div>
                @endforeach
            @else
                <div class="empty-chat">
                    <i class="ph-fill ph-chat-circle-text"></i>
                    <h3>Start a Conversation</h3>
                    <p>Send us a message and we'll get back to you as soon as possible!</p>
                </div>
            @endif
        </div>

        <div id="typingIndicator" class="typing-indicator" style="display: none;">
            <div class="typing-dots"><span></span><span></span><span></span></div>
            <span>Support is typing...</span>
        </div>

        <div class="quick-replies">
            <button class="quick-reply-btn" onclick="useQuickReply(this)">I want to cancel my order</button>
            <button class="quick-reply-btn" onclick="useQuickReply(this)">Check my order status</button>
            <button class="quick-reply-btn" onclick="useQuickReply(this)">Product inquiry</button>
            <button class="quick-reply-btn" onclick="useQuickReply(this)">Payment issue</button>
        </div>

        <div id="filePreviewContainer"></div>

        <div class="chat-input-section">
            <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="handleFileSelect(event)">
            <button type="button" class="attach-btn" onclick="document.getElementById('fileInput').click()" title="Attach image">
                <i class="ph-bold ph-image"></i>
            </button>
            <div class="chat-input-wrapper">
                <input type="text" id="messageInput" class="chat-input-field" placeholder="Type your message here..." autocomplete="off">
            </div>
            <button type="button" id="sendBtn" class="btn-send" onclick="sendMessage()" title="Send message">
                <i class="ph-bold ph-paper-plane-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const typingIndicator = document.getElementById('typingIndicator');
let lastMessageCount = {{ $chats->count() }};

// Scroll to bottom on load
scrollToBottom();

// Enter key to send
messageInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') sendMessage();
});

function useQuickReply(btn) {
    const text = btn.textContent;
    messageInput.value = text;
    sendMessage();
}

function playNotificationSound() {
    try {
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        if (ctx.state === 'suspended') ctx.resume();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(800, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(600, ctx.currentTime + 0.2);
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.3);
    } catch(e) {}
}

async function sendMessage() {
    const message = messageInput.value.trim();
    const fileInput = document.getElementById('fileInput');
    const imageFile = fileInput.files && fileInput.files[0];

    if (!message && !imageFile) return;

    const originalMessage = message;

    messageInput.value = '';
    sendBtn.disabled = true;

    try {
        const formData = new FormData();

        if (message) {
            formData.append('message', message);
        }

        if (imageFile) {
            formData.append('image', imageFile);
        }

        const response = await fetch('{{ route('customer.chat.send') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Clear selected file after successful sending
            fileInput.value = '';
            document.getElementById('filePreviewContainer').innerHTML = '';

            // Refresh the message list so the sent message
            // is loaded exactly once from the server.
            const refreshResponse = await fetch('{{ route('customer.chat.messages') }}');
            const refreshData = await refreshResponse.json();

            if (refreshData.success) {
                chatMessages.innerHTML = '';

                if (refreshData.chats.length > 0) {
                    let previousDate = null;

                    refreshData.chats.forEach(chat => {
                        const messageDate = new Date(chat.created_at).toISOString().split('T')[0];

                        if (messageDate !== previousDate) {
                            const dateSeparator = document.createElement('div');
                            dateSeparator.className = 'date-separator';
                            dateSeparator.innerHTML = `
                                <span>${new Date(chat.created_at).toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: '2-digit',
                                    year: 'numeric'
                                })}</span>
                            `;
                            chatMessages.appendChild(dateSeparator);
                            previousDate = messageDate;
                        }

                        addMessage(
                            chat.message,
                            chat.sender,
                            formatTime(chat.created_at),
                            chat.image_path,
                            true
                        );
                    });
                } else {
                    chatMessages.innerHTML = `
                        <div class="empty-chat">
                            <i class="ph-fill ph-chat-circle-text"></i>
                            <h3>Start a Conversation</h3>
                            <p>Send us a message and we'll get back to you as soon as possible!</p>
                        </div>
                    `;
                }

                lastMessageCount = refreshData.chats.length;
                scrollToBottom();
            }

        } else {
            console.error('Send failed:', data);
            messageInput.value = originalMessage;
        }

    } catch (error) {
        console.error('Error:', error);
        messageInput.value = originalMessage;
    }

    sendBtn.disabled = false;
}

function addMessage(text, sender, time, imagePath = null, skipScroll = false) {
    const emptyChat = document.querySelector('.empty-chat');
    if (emptyChat) emptyChat.remove();

    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${sender}`;
    let avatarHtml = sender === 'admin' ? '<div class="message-avatar">S</div>' : '';

    const safeText = text ? escapeHtml(text) : '';
    const imageHtml = imagePath
        ? `<div style="margin-bottom:0.5rem;"><img src="/storage/${imagePath}" style="max-width:100%; height:auto; border-radius:12px; border:1px solid rgba(255,255,255,0.25);"></div>`
        : '';

    messageDiv.innerHTML = `
        <div class="message-content">${sender === 'admin' ? avatarHtml : ''}<div class="message-bubble">${imageHtml}${safeText}</div></div>
        <div class="message-time">${time}</div>`;
    chatMessages.appendChild(messageDiv);
    if (!skipScroll) scrollToBottom();
}

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

// Auto-refresh messages
setInterval(async function() {
    try {
        const response = await fetch('{{ route('customer.chat.messages') }}');
        const data = await response.json();

        if (data.success) {
            // Check for typing status
            if (data.typing) {
                typingIndicator.style.display = 'flex';
            } else {
                typingIndicator.style.display = 'none';
            }

            if (data.chats.length > lastMessageCount) {
                typingIndicator.style.display = 'none';
                data.chats.forEach((chat, index) => {
                    if (index >= lastMessageCount) {
                        addMessage(chat.message, chat.sender, formatTime(chat.created_at), chat.image_path, true);
                    }
                });
                playNotificationSound();
                scrollToBottom();
                lastMessageCount = data.chats.length;
            }
        }
    } catch (error) { console.error('Error:', error); }
}, 3000);

// Check for new admin messages every 3 seconds

// File upload handling
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        alert('Image size must be less than 5MB.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const container = document.getElementById('filePreviewContainer');
        container.innerHTML = `
            <div class="file-preview">
                <img src="${e.target.result}" alt="Preview">
                <span class="file-name">${file.name}</span>
                <button type="button" class="remove-file" onclick="removeFile()"><i class="ph-bold ph-x"></i></button>
            </div>`;
        container.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreviewContainer').innerHTML = '';
}

// Chat Mute/Unmute
let chatMuted = localStorage.getItem('chatMuted') === 'true';

function updateMuteIcon() {
    const icon = document.getElementById('chatMuteIcon');
    if (chatMuted) {
        icon.className = 'ph-bold ph-bell-slash';
        icon.style.color = '#a1887f';
        icon.style.opacity = '0.5';
    } else {
        icon.className = 'ph-bold ph-bell';
        icon.style.color = '#6d4c41';
        icon.style.opacity = '1';
    }
}

function toggleChatMute(event) {
    event.stopPropagation();
    chatMuted = !chatMuted;
    localStorage.setItem('chatMuted', chatMuted);
    updateMuteIcon();
}

// Initialize mute state on load
(function() {
    updateMuteIcon();
})();
</script>
@endsection