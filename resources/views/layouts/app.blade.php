<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SQUIFM') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!-- Styles -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Modern, Clean, Professional SQUIFM Theme (Brown & White) */
            :root {
                --primary: #a1887f; /* Brown 300 */
                --primary-dark: #6d4c41; /* Brown 800 */
                --primary-deep: #4e342e; /* Brown 900 */
                --secondary: #fffdfa; /* Off White */
                --accent: #ffe0b2; /* Light Brown/Beige */
                --white: #ffffff;
                --text-dark: #4e342e; /* Brown 900 */
                --text-medium: #795548; /* Brown 600 */
                --text-light: #a1887f; /* Brown 300 */
                --bg-body: #fffdfa; /* Off White */
                --danger: #ef4444;
                --warning: #f59e0b;
                --success: #a1887f;
                --border-radius: 0.75rem;
            }

            body { 
                font-family: 'Instrument Sans', 'Inter', system-ui, -apple-system, sans-serif; 
                background-color: var(--bg-body); 
                color: var(--text-dark);
                margin: 0;
                padding: 0;
                line-height: 1.6;
            }

            * { box-sizing: border-box; }

            /* Layout */
            .flex { display: flex; }
            .flex-col { flex-direction: column; }
            .items-center { align-items: center; }
            .justify-center { justify-content: center; }
            .justify-between { justify-content: space-between; }
            .justify-end { justify-content: flex-end; }
            .gap-4 { gap: 1rem; }
            .gap-6 { gap: 1.5rem; }
            .w-full { width: 100%; }
            .min-h-screen { min-height: 100vh; }
            .mx-auto { margin-left: auto; margin-right: auto; }
            .max-w-md { max-width: 28rem; }
            
            /* Typography */
            .text-3xl { font-size: 1.875rem; line-height: 2.25rem; font-weight: 700; color: var(--primary-deep); }
            .text-2xl { font-size: 1.5rem; line-height: 2rem; font-weight: 700; }
            .text-xl { font-size: 1.25rem; line-height: 1.75rem; font-weight: 600; }
            .text-lg { font-size: 1.125rem; line-height: 1.75rem; font-weight: 600; }
            .text-sm { font-size: 0.875rem; }
            .text-center { text-align: center; }
            .font-bold { font-weight: 700; }
            .font-medium { font-weight: 500; }
            
            .text-brown-800 { color: var(--primary-deep); }
            .text-brown-700 { color: var(--primary-dark); }
            .text-brown-600 { color: var(--primary); }
            .text-gray-600 { color: var(--text-medium); }
            .text-gray-700 { color: #334155; }
            .text-white { color: var(--white); }
            .text-red-600 { color: var(--danger); }
            
            /* Sidebar */
            .wrapper { display: flex; min-height: 100vh; }
            .sidebar { 
                width: 260px; 
                background: var(--white);
                border-right: 1px solid #e2e8f0;
                color: var(--text-medium); 
                padding: 1.5rem; 
                display: flex;
                flex-direction: column;
                position: fixed;
                height: 100%;
                top: 0;
                left: 0;
                z-index: 50;
            }
            .sidebar h2 { 
                color: var(--primary-dark); 
                font-size: 1.5rem; 
                margin-bottom: 2rem; 
                display: flex; 
                align-items: center; 
                gap: 0.5rem;
                padding-left: 0.5rem;
            }
            .sidebar h2::before {
                content: '';
                display: block;
                width: 12px;
                height: 12px;
                background: var(--primary);
                border-radius: 50%;
            }
            .sidebarnav { margin-top: 1rem; }
            .sidebar a { 
                display: block; 
                padding: 0.85rem 1rem; 
                color: var(--text-medium); 
                text-decoration: none; 
                border-radius: 0.5rem; 
                margin-bottom: 0.5rem;
                transition: all 0.2s;
                font-weight: 500;
                border-left: 3px solid transparent;
            }
            .sidebar a:hover { 
                background-color: var(--secondary); 
                color: var(--primary-dark); 
            }
            .sidebar a.active { 
                background-color: var(--secondary); 
                color: var(--primary-dark); 
                border-left-color: var(--primary);
            }
            
            /* Main Content */
            .main-content { 
                flex: 1; 
                padding: 2.5rem; 
                margin-left: 0; 
                background-color: var(--bg-body); 
            }
            
            /* Cards */
            .bg-white, .card { background-color: var(--white); }
            .shadow, .card { 
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02); 
                border-radius: var(--border-radius);
                border: 1px solid #e2e8f0;
            }
            .p-6, .card { padding: 1.5rem; }
            .rounded-lg { border-radius: var(--border-radius); }
            
            /* Grid Logic */
            .grid { display: grid; }
            .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
            .gap-6 { gap: 1.5rem; }
            @media (min-width: 768px) {
                .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (min-width: 1024px) {
                .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            }
            
            /* Buttons */
            button, .btn {
                cursor: pointer;
                transition: all 0.2s;
                font-family: inherit;
            }
            .bg-brown-600, .btn-primary { 
                background-color: var(--primary);
                color: white; 
                border: none;
                border-radius: 0.5rem;
                padding: 0.6rem 1.2rem;
                font-weight: 600;
                box-shadow: 0 2px 4px rgba(141,110,99,0.18);
            }
            .bg-brown-600:hover, .btn-primary:hover { 
                background-color: var(--primary-dark); 
                transform: translateY(-1px);
            }
            .bg-red-600 { background-color: var(--danger); }
            .text-brown-600 { color: var(--primary-dark); }
            
            /* Form Elements */
            input, select {
                width: 100%;
                padding: 0.75rem;
                border: 1px solid #cbd5e1;
                border-radius: 0.5rem;
                margin-top: 0.25rem;
                box-sizing: border-box;
                font-size: 1rem;
            }
            input:focus, select:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            }
            label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-medium); margin-bottom: 0.25rem; }
            
            /* Tables */
            table { width: 100%; border-collapse: separate; border-spacing: 0; }
            th { text-align: left; padding: 1rem; border-bottom: 2px solid #e2e8f0; color: var(--text-light); font-weight: 600; font-size: 0.9rem; }
            td { padding: 1rem; border-bottom: 1px solid #f1f5f9; color: var(--text-medium); }
            tr:last-child td { border-bottom: none; }

            /* Utilities helpers based on Tailwind */
            .mb-2 { margin-bottom: 0.5rem; }
            .mb-4 { margin-bottom: 1rem; }
            .mb-6 { margin-bottom: 1.5rem; }
            .mb-10 { margin-bottom: 2.5rem; }
            .mt-1 { margin-top: 0.25rem; }
            .mt-2 { margin-top: 0.5rem; }
            .mt-4 { margin-top: 1rem; }
            .mt-6 { margin-top: 1.5rem; }
            .mt-8 { margin-top: 2rem; }
            .mr-2 { margin-right: 0.5rem; }
            .pl-2 { padding-left: 0.5rem; }
            .pt-6 { padding-top: 1.5rem; }
            .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .w-3 { width: 0.75rem; }
            .h-3 { height: 0.75rem; }
            .h-6 { height: 1.5rem; }
            .rounded-full { border-radius: 9999px; }
            .rounded-md { border-radius: 0.375rem; }
            .border { border-width: 1px; border-style: solid; }
            .border-t { border-top-width: 1px; }
            .border-brown-200 { border-color: #d7ccc8; }
            .border-brown-300 { border-color: #a1887f; }
            .bg-brown-500 { background-color: var(--primary); }
            .bg-gray-200 { background-color: #e2e8f0; }
            .text-left { text-align: left; }
            .block { display: block; }
            .hidden { display: none; }
            
            /* Responsive */
            @media (max-width: 768px) {
                .sidebar { display: none; } /* Hide sidebar on mobile for now, or implement hamburger */
                .main-content { margin-left: 0; padding: 1rem; }
                .grid-cols-1 { grid-template-columns: 1fr !important; }
            }
        </style>
    @endif
</head>
<body class="bg-gray-100 min-h-screen">
    @yield('content')

    {{-- Order Notification: badge counter + bell sound --}}
    <script>
    (function () {
        var lastCount  = parseInt(localStorage.getItem('sqfm_pending') || '-1', 10);
        var lastId     = parseInt(localStorage.getItem('sqfm_last_id') || '0',  10);
        var lastChatId = parseInt(localStorage.getItem('sqfm_last_chat_id') || '0', 10);
        var firstLoad  = true;

        function playBell() {
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                // Two-tone ding-dong
                [880, 660].forEach(function(freq, i) {
                    var osc  = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = 'sine';
                    var t = ctx.currentTime + i * 0.35;
                    osc.frequency.setValueAtTime(freq, t);
                    osc.frequency.exponentialRampToValueAtTime(freq * 0.6, t + 0.6);
                    gain.gain.setValueAtTime(0, t);
                    gain.gain.linearRampToValueAtTime(0.55, t + 0.04);
                    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.9);
                    osc.start(t);
                    osc.stop(t + 0.9);
                });
            } catch(e) {}
        }

        function updateBadge(count) {
            var badge = document.getElementById('msg-nav-badge');
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function poll() {
            fetch('/api/orders/pending', { credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var count  = data.count  || 0;
                    var latestId = data.lastId || 0;
                    var chatCount = data.chatCount || 0;
                    var latestChatId = data.lastChatId || 0;

                    var totalBadgeCount = count + chatCount;
                    updateBadge(totalBadgeCount);

                    // Ring bell only when a genuinely NEW order or NEW chat arrives (not on first page load)
                    if (!firstLoad) {
                        if (latestId > lastId) {
                            playBell();
                            if (typeof notificationSystem !== 'undefined') {
                                notificationSystem.order('New order received!');
                            }
                        }
                        if (latestChatId > lastChatId) {
                            playBell();
                            if (typeof notificationSystem !== 'undefined') {
                                notificationSystem.chat('New message from customer');
                            }
                        }
                    }

                    firstLoad = false;
                    lastCount = count;
                    lastId    = latestId;
                    lastChatId = latestChatId;
                    localStorage.setItem('sqfm_pending', count);
                    localStorage.setItem('sqfm_last_id', latestId);
                    localStorage.setItem('sqfm_last_chat_id', latestChatId);
                })
                .catch(function() {});
        }

        document.addEventListener('DOMContentLoaded', poll);
        setInterval(poll, 15000); // poll every 15 s
    })();
    </script>

    <!-- Notification System Scripts -->
    <link rel="stylesheet" href="{{ asset('notification-styles.css') }}">
    <script src="{{ asset('notification-system.js') }}"></script>
    <script src="{{ asset('notification-helpers.js') }}"></script>
    <script src="{{ asset('notification-events.js') }}"></script>
    @stack('scripts')
</body>
</html>