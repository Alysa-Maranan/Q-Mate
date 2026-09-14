<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SQUIFM') }}</title>
    
    <!-- Optimized Styles - Single CSS file -->
    <link rel="stylesheet" href="{{ asset('fast-styles.css') }}">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="{{ asset('fast-styles.css') }}" as="style">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
</head>
<body>
    @yield('content')

    <!-- Minimal JavaScript - Only essential functions -->
    <script>
    // Fast navigation with minimal overhead
    (function() {
        'use strict';
        
        // Simple notification badge update
        function updateBadge() {
            const badge = document.getElementById('msg-nav-badge');
            if (!badge) return;
            
            // Simple localStorage check instead of API call
            const count = parseInt(localStorage.getItem('pending_orders') || '0');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        }
        
        // Optimize page transitions
        function optimizeNavigation() {
            const links = document.querySelectorAll('.nav-item');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Add loading state
                    this.style.opacity = '0.7';
                    this.textContent = 'Loading...';
                });
            });
        }
        
        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            updateBadge();
            optimizeNavigation();
        });
        
        // Reduce polling frequency to improve performance
        setInterval(updateBadge, 30000); // Every 30 seconds instead of 15
        
    })();
    </script>
</body>
</html>